<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use HasSeo;
    use InteractsWithMedia;

    // --- Медиа (обложка и галерея)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('images');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)->height(600)
            ->format('webp')->quality(82)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)->format('webp')->quality(80)
            ->nonQueued();
    }

    public function coverUrl(string $conv = 'large'): ?string
    {
        $m = $this->getFirstMedia('cover') ?: $this->getFirstMedia('images');
        return $m?->getUrl($conv);
    }

    public function galleryUrls(string $conv = 'thumb'): array
    {
        return $this->getMedia('images')->map(fn($m) => $m->getUrl($conv))->all();
    }

    // --- Атрибуты
    protected $fillable = [
        'category_id','brand_id','name','slug','description',
        'is_published','published_at','price','sort','sku',

        'discount_percent','discount_is_forever','discount_starts_at','discount_ends_at',
    ];

    protected $casts = [
        'category_id'          => 'int',
        'brand_id'             => 'int',
        'is_published'         => 'bool',
        'published_at'         => 'datetime',
        'price'                => 'decimal:2',

        'discount_percent'     => 'int',
        'discount_is_forever'  => 'bool',
        'discount_starts_at'   => 'datetime',
        'discount_ends_at'     => 'datetime',
    ];

    protected $with = ['media'];

    protected function discountStartsAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->setTimezone(config('app.timezone')) : null,
            set: fn ($value) => $value ? Carbon::parse($value, config('app.timezone'))->utc() : null,
        );
    }

    protected function discountEndsAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->setTimezone(config('app.timezone')) : null,
            set: fn ($value) => $value ? Carbon::parse($value, config('app.timezone'))->utc() : null,
        );
    }

    // --- Связи
    public function category() { return $this->belongsTo(Category::class); }
    public function brand()    { return $this->belongsTo(Brand::class); }
    public function attributesValues() { return $this->hasMany(ProductAttributeValue::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product')
            ->withTimestamps()
            ->withPivot('sort')
            ->orderBy('pivot_sort');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeWithActiveDiscount(Builder $q): Builder
    {
        $nowUtc = now('UTC');

        return $q->whereNotNull('discount_percent')
            ->where('discount_percent', '>', 0)
            ->where(function ($w) use ($nowUtc) {
                $w->whereNull('discount_starts_at')
                    ->orWhere('discount_starts_at', '<=', $nowUtc);
            })
            ->where(function ($w) use ($nowUtc) {
                $w->where('discount_is_forever', true)
                    ->orWhereNull('discount_ends_at')
                    ->orWhere('discount_ends_at', '>=', $nowUtc);
            });
    }

    // --- Помощники
    public function hasActiveDiscount(): bool
    {
        if (empty($this->discount_percent) || $this->discount_percent <= 0) {
            return false;
        }

        $nowAppTz = now(config('app.timezone'));
        $startsOk = (!$this->discount_starts_at) || $this->discount_starts_at->lte($nowAppTz);
        $endsOk   = $this->discount_is_forever || (!$this->discount_ends_at) || $this->discount_ends_at->gte($nowAppTz);

        return $startsOk && $endsOk;
    }

    public function discountPrice(): ?float
    {
        if (! $this->hasActiveDiscount()) return null;

        $pct = min(100, max(1, (int) $this->discount_percent));
        $final = (float) $this->price * (100 - $pct) / 100;

        return max(0, round($final, 2));
    }

    public function finalPrice(): float
    {
        return $this->discountPrice() ?? (float) $this->price;
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!trim($term)) return $q;
        $term = mb_strtolower($term);
        return $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
    }

    public function scopeFilter(Builder $q, array $f): Builder
    {
        return $q
            ->when(!empty($f['brand_id']), fn ($q) => $q->where('brand_id', $f['brand_id']))
            ->when(!empty($f['type']), function ($q) use ($f) {
                $q->whereHas('attributesValues', function ($w) use ($f) {
                    $w->whereHas('attribute', fn ($a) => $a->where('code', 'type'))
                        ->where(function ($cond) use ($f) {
                            $cond->where('value_text', $f['type'])
                                ->orWhereHas('attributeValue', fn ($av) => $av->where('slug', $f['type']));
                        });
                });
            })
            ->when(isset($f['price_min']) && $f['price_min'] !== '', fn ($q) => $q->where('price', '>=', (float) $f['price_min']))
            ->when(isset($f['price_max']) && $f['price_max'] !== '', fn ($q) => $q->where('price', '<=', (float) $f['price_max']));
    }

    public function scopeSortBy(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc'  => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            'type'       => $q->leftJoin('product_attribute_values as pav', function ($join) {
                $join->on('pav.product_id', '=', 'products.id');
            })->leftJoin('attributes as a', function ($join) {
                $join->on('a.id', '=', 'pav.attribute_id')->where('a.code', 'type');
            })->orderByRaw('COALESCE(pav.value_text, \'\') ASC')
                ->select('products.*'),
            'popular', null, '' => $q->orderBy('published_at', 'desc'),
            default      => $q->orderBy('published_at', 'desc'),
        };
    }
}
