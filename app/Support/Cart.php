<?php

namespace App\Support;

use Illuminate\Support\Arr;

class Cart
{
    protected string $key = 'cart.items';

    public function all(): array
    {
        return session($this->key, []);
    }

    public function put(int $productId, int $qty = 1): array
    {
        $items = $this->all();
        $qty   = max(1, $qty);

        $items[$productId] = [
            'product_id' => $productId,
            'qty'        => $qty,
        ];

        session([$this->key => $items]);
        return $items;
    }

    public function add(int $productId, int $delta = 1): array
    {
        $items = $this->all();
        $row   = $items[$productId] ?? ['product_id' => $productId, 'qty' => 0];
        $row['qty'] = max(1, (int)$row['qty'] + (int)$delta);

        $items[$productId] = $row;
        session([$this->key => $items]);
        return $items;
    }

    public function update(int $productId, int $qty): array
    {
        $items = $this->all();
        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = ['product_id' => $productId, 'qty' => $qty];
        }
        session([$this->key => $items]);
        return $items;
    }

    public function remove(int $productId): array
    {
        $items = $this->all();
        unset($items[$productId]);
        session([$this->key => $items]);
        return $items;
    }

    /** batch = [['product_id'=>1,'qty'=>2], ...]  */
    public function sync(array $batch): array
    {
        $clean = [];
        foreach ($batch as $row) {
            $pid = (int) Arr::get($row, 'product_id');
            $qty = (int) Arr::get($row, 'qty', 1);
            if ($pid > 0 && $qty > 0) {
                $clean[$pid] = ['product_id' => $pid, 'qty' => $qty];
            }
        }
        session([$this->key => $clean]);
        return $clean;
    }

    public function clear(): void
    {
        session()->forget($this->key);
    }
}
