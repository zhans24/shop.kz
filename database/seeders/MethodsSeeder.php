<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\DeliveryMethod::firstOrCreate(['name'=>'Самовывоз'],        ['price'=>0,'is_active'=>true]);
        \App\Models\DeliveryMethod::firstOrCreate(['name'=>'Курьер по городу'], ['price'=>1500,'is_active'=>true]);
        \App\Models\DeliveryMethod::firstOrCreate(['name'=>'ТК по РК'],         ['price'=>2500,'is_active'=>true]);

        \App\Models\PaymentMethod::firstOrCreate(['name'=>'Наличные'],           ['is_active'=>true]);
        \App\Models\PaymentMethod::firstOrCreate(['name'=>'Карта'],              ['is_active'=>true]);
        \App\Models\PaymentMethod::firstOrCreate(['name'=>'Kaspi QR'], ['is_active'=>true]);
    }

}
