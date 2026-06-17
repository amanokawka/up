<?php

namespace Database\Seeders;

use App\Models\Roli;
use Illuminate\Database\Seeder;

class RoliSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['code' => 'user', 'nazvanie' => 'Пользователь'],
            ['code' => 'moderator', 'nazvanie' => 'Модератор'],
            ['code' => 'admin', 'nazvanie' => 'Администратор'],
        ];

        foreach ($roles as $role) {
            Roli::firstOrCreate(
                ['code' => $role['code']],  // Условие поиска
                ['nazvanie' => $role['nazvanie']]  // Данные для создания
            );
        }
    }
}