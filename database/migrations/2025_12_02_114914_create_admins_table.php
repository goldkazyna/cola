<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Создаём первого админа
        \App\Models\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@coca-cola.kz',
            'password' => Hash::make('admin123'),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};