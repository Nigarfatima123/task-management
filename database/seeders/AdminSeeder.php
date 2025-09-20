<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => '$2y$10$baFgOMidN3o10vVv6yMfOOinFJ63OTMSJPCZbj1tI5MEY5GsKLCse', // bcrypt for '12345678'
        ]);
    }
}
