<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::firstOrCreate( ['code' => 'color'], ['name' => 'Цвет корпуса', 'type' => 'text', 'is_filterable' => true, 'sort' => 10] );
        Attribute::firstOrCreate( ['code' => 'material'], ['name' => 'Материал корпуса', 'type' => 'text', 'is_filterable' => true, 'sort' => 20] );
    }
}
