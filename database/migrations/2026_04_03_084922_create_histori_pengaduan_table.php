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
        Schema::create('histori_pengaduan', function (Blueprint $table) {
            $table->id('id_histori');
        
            $table->unsignedBigInteger('id_pengaduan');
        
            $table->enum('status', ['menunggu', 'diproses', 'selesai']);
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_update')->useCurrent();
        
            $table->timestamps();
        
            // Relasi
            $table->foreign('id_pengaduan')->references('id_pengaduan')->on('pengaduan')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_pengaduan');
    }
};
