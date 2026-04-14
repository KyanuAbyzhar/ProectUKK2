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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
        
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kategori');
        
            $table->string('judul');
            $table->text('deskripsi');
            $table->date('tanggal_pengaduan');
        
            $table->enum('status', ['menunggu', 'diproses', 'selesai'])
                  ->default('menunggu');
        
            $table->string('foto')->nullable();
        
            $table->timestamps();
        
            // Relasi
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
