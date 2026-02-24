<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * KUK 024: Migrasi - Database seeding
     */
    public function run(): void
    {
        // Database kosong untuk presentasi
        // User akan menambahkan data secara manual saat demo

        $this->command->info('✅ Database siap - kosong untuk presentasi!');
    }
}
