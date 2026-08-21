<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('category')->default('lainnya'); // kendaraan, ruang_rapat, peralatan, lainnya
            $table->text('description')->nullable();
            $table->string('status')->default('tersedia'); // tersedia, dipinjam, maintenance
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
