<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('full_name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
