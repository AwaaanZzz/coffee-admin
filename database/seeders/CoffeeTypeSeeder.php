<?php

namespace Database\Seeders;

use App\Models\CoffeeType;
use Illuminate\Database\Seeder;

class CoffeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: ganti nama2 ini sesuai 11 kopi yang sebenarnya
        $coffees = [
            ['name' => 'Robusta Lampung', 'category' => 'robusta'],
            ['name' => 'Robusta Temanggung', 'category' => 'robusta'],
            ['name' => 'Robusta Bali', 'category' => 'robusta'],
            ['name' => 'Robusta Flores', 'category' => 'robusta'],
            ['name' => 'Robusta Dampit', 'category' => 'robusta'],
            ['name' => 'Arabika Gayo', 'category' => 'arabika'],
            ['name' => 'Arabika Toraja', 'category' => 'arabika'],
            ['name' => 'Arabika Kintamani', 'category' => 'arabika'],
            ['name' => 'Arabika Mandailing', 'category' => 'arabika'],
            ['name' => 'Arabika Java Preanger', 'category' => 'arabika'],
            ['name' => 'Arabika Wamena', 'category' => 'arabika'],
        ];

        foreach ($coffees as $coffee) {
            CoffeeType::create($coffee);
        }
    }
}
