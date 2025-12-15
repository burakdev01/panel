<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('LanguagesSeeder');
        $this->call('AdminsSeeder');
        $this->call('SlidersSeeder');
        $this->call('PostsSeeder');
        $this->call('PostCommentsSeeder');
    }
}
