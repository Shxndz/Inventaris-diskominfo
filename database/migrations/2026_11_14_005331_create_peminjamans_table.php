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
       Schema::create('peminjamans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('barang_id')->constrained()->cascadeOnDelete();
    $table->foreignId('ruangan_id')->constrained('ruangans'); // ruangan tujuan
    $table->unsignedInteger('jumlah')->default(1);
    $table->dateTime('tgl_pinjam');
    $table->dateTime('tgl_kembali');
    $table->text('keperluan');
    $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])->default('pending');
    $table->text('catatan_admin')->nullable();
    $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
    $table->dateTime('tgl_dikembalikan')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
