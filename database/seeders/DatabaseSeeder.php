<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Activity;
use App\Models\Organization;
use App\Models\OrganizationPhone;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем здания
        $buildings = [
            [
                'address' => 'г. Москва, ул. Ленина 1, офис 3',
                'latitude' => 55.7558,
                'longitude' => 37.6176,
            ],
            [
                'address' => 'г. Москва, ул. Тверская 10',
                'latitude' => 55.7600,
                'longitude' => 37.6100,
            ],
            [
                'address' => 'г. Москва, ул. Арбат 25',
                'latitude' => 55.7500,
                'longitude' => 37.5900,
            ],
            [
                'address' => 'г. Москва, ул. Блюхера 32/1',
                'latitude' => 55.7400,
                'longitude' => 37.5800,
            ],
        ];

        foreach ($buildings as $buildingData) {
            Building::create($buildingData);
        }

        // Создаем деятельности (иерархия)
        $activities = [
            // Уровень 1
            ['name' => 'Еда', 'parent_id' => null, 'level' => 1],
            ['name' => 'Автомобили', 'parent_id' => null, 'level' => 1],
            ['name' => 'Одежда', 'parent_id' => null, 'level' => 1],
            
            // Уровень 2 - Еда
            ['name' => 'Мясная продукция', 'parent_id' => 1, 'level' => 2],
            ['name' => 'Молочная продукция', 'parent_id' => 1, 'level' => 2],
            ['name' => 'Хлебобулочные изделия', 'parent_id' => 1, 'level' => 2],
            
            // Уровень 2 - Автомобили
            ['name' => 'Грузовые', 'parent_id' => 2, 'level' => 2],
            ['name' => 'Легковые', 'parent_id' => 2, 'level' => 2],
            
            // Уровень 3 - Автомобили
            ['name' => 'Запчасти', 'parent_id' => 8, 'level' => 3],
            ['name' => 'Аксессуары', 'parent_id' => 8, 'level' => 3],
        ];

        foreach ($activities as $activityData) {
            Activity::create($activityData);
        }

        // Создаем организации
        $organizations = [
            [
                'name' => 'ООО "Рога и Копыта"',
                'building_id' => 1,
                'phones' => ['2-222-222', '3-333-333'],
                'activities' => [1, 4, 5], // Еда, Мясная продукция, Молочная продукция
            ],
            [
                'name' => 'ИП Иванов А.А.',
                'building_id' => 2,
                'phones' => ['8-923-666-13-13'],
                'activities' => [2, 7], // Автомобили, Грузовые
            ],
            [
                'name' => 'ООО "АвтоСервис"',
                'building_id' => 3,
                'phones' => ['4-444-444', '5-555-555'],
                'activities' => [2, 8, 9, 10], // Автомобили, Легковые, Запчасти, Аксессуары
            ],
            [
                'name' => 'ООО "Молочный Мир"',
                'building_id' => 4,
                'phones' => ['6-666-666'],
                'activities' => [1, 5], // Еда, Молочная продукция
            ],
            [
                'name' => 'ИП Петров В.В.',
                'building_id' => 1,
                'phones' => ['7-777-777'],
                'activities' => [3], // Одежда
            ],
        ];

        foreach ($organizations as $orgData) {
            $organization = Organization::create([
                'name' => $orgData['name'],
                'building_id' => $orgData['building_id'],
            ]);

            // Добавляем телефоны
            foreach ($orgData['phones'] as $phone) {
                OrganizationPhone::create([
                    'organization_id' => $organization->id,
                    'phone' => $phone,
                ]);
            }

            // Добавляем деятельности
            $organization->activities()->attach($orgData['activities']);
        }
    }
}
