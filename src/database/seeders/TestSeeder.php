<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        DB::table('tests')->insert([
            [
                'id'=>Str::uuid(),
                'user_name'=>'豚座おちゃ',
                'user_id'=>'InokozaOcha',
                'user_password'=>'Ocha123',
            ],
            [
                'id'=>Str::uuid(),
                'user_name'=>'ぷれすとん',
                'user_id'=>'preston',
                'user_password'=>'preston123',
            ]

        ]);
    }
}
