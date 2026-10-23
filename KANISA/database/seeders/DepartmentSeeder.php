<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name_sw' => 'Idara ya Wamama (WWI)',
                'name_en' => 'Women\'s Department (WWI)',
                'description_sw' => 'Idara ya ushirika wa wamama (WWI).',
                'description_en' => 'Women\'s fellowship department (WWI).',
                'icon' => 'fa-female',
                'color' => '#ec4899',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_sw' => 'Idara ya Wababa',
                'name_en' => 'Men\'s Department',
                'description_sw' => 'Idara ya ushirika wa wababa.',
                'description_en' => 'Men\'s fellowship department.',
                'icon' => 'fa-male',
                'color' => '#1e40af',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_sw' => 'Idara ya Vijana (CGM)',
                'name_en' => 'Youth Department (CGM)',
                'description_sw' => 'Idara inayokutanisha vijana (CGM) kwa ajili ya kumtumikia Mungu.',
                'description_en' => 'Youth department (CGM) serving God.',
                'icon' => 'fa-users',
                'color' => '#10b981',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_sw' => 'Idara ya Watoto (CWI)',
                'name_en' => 'Children\'s Department (CWI)',
                'description_sw' => 'Idara inayoshughulika na malezi ya kiroho kwa watoto (CWI).',
                'description_en' => 'Children\'s spiritual upbringing department (CWI).',
                'icon' => 'fa-child',
                'color' => '#3b82f6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_sw' => 'Idara ya Praise & Kwaya (KUSKI)',
                'name_en' => 'Praise & Choir Department (KUSKI)',
                'description_sw' => 'Idara inayoshughulika na sifa, kuabudu na uimbaji wa kwaya (KUSKI).',
                'description_en' => 'Praise, worship and choir singing department (KUSKI).',
                'icon' => 'fa-music',
                'color' => '#8b5cf6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_sw' => 'Idara ya Ujenzi',
                'name_en' => 'Construction Department',
                'description_sw' => 'Idara inayoshughulika na ujenzi na maendeleo ya kanisa.',
                'description_en' => 'Department responsible for church construction and development.',
                'icon' => 'fa-hammer',
                'color' => '#f59e0b',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('departments')->insert($departments);
    }
}
