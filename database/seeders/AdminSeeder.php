<?php

namespace Database\Seeders;

use App\Models\Polzovateli;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Проверяем, существует ли уже пользователь с таким логином
        $user = Polzovateli::where('login', 'amanokawa')->first();
        
        if (!$user) {
            // Если нет - создаём
            Polzovateli::create([
                'login' => 'amanokawa',
                'parol' => Hash::make('edfdsc'),
                'imya' => 'Амано',
                'rol_id' => 3,
            ]);
            $this->command->info('✅ Администратор создан!');
        } else {
            // Если есть - обновляем пароль (на всякий случай)
            $user->update([
                'parol' => Hash::make('edfdsc'),
                'imya' => 'Амано',
                'rol_id' => 3,
            ]);
            $this->command->info('🔄 Администратор обновлён!');
        }
    }
}