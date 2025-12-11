<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\AdminModel;

class AdminsSeeder extends Seeder
{
    public function run(): void
    {
        $adminModel = new AdminModel();

        $existing = $adminModel->where('username', 'burak')->first();
        if ($existing) {
            echo "Admin 'burak' already exists. Skipping.\n";
            return;
        }

        $adminModel->insert([
            'username' => 'burak',
            'password' => '$2y$12$HPENkzSMxPUHZasMvDr42uBC5WPq4Lmt9NMOpJ2Q43RHQvFbrcIs.',
        ]);
    }
}
