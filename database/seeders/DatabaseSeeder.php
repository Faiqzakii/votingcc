<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => bcrypt('mubarok'),
        ]);

        // Create 20 Employees
        //User::factory(20)->create();

        // Create Candidates
        /*
        $candidates = [
            'Budi Santoso', 'Siti Aminah', 'Rahmat Hidayat', 'Dewi Lestari', 'Agus Kurniawan', 'Sri Wahyuni'
        ];

        foreach ($candidates as $candidate) {
            \App\Models\Candidate::create([
                'name' => $candidate,
                'description' => 'Visi misi calon change champion...',
                'photo_path' => 'https://ui-avatars.com/api/?name=' . urlencode($candidate) . '&background=random',
            ]);
        }
        */
    }
}
