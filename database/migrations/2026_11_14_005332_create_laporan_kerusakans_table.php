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
        Schema::create('laporan_kerusakans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('barang_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ruangan_id')->constrained('ruangans');
    $table->text('deskripsi');
    $table->string('foto')->nullable();
    $table->enum('status', ['dilaporkan', 'diperbaiki', 'selesai'])->default('dilaporkan');
    $table->text('catatan_admin')->nullable();
    $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakans');
    }
};
