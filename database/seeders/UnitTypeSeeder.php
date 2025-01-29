<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public const TYPES = [
        'adult' => 'adult',
        'youth' => 'youth',
        'infant' => 'infant',
        'child' => 'child',
        'senior' => 'senior',
        'guest' => 'guest',
        'group' => 'group',
    ];

    public function run()
    {
        foreach (self::TYPES as $type) {
            UnitType::factory()->create([
                'name' => $type,
                'internal_name' => $type,
                'description' => $type,
            ]);
        }
    }
}
