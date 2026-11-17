<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang', 'nama', 'kategori_barang_id',
        'ruangan_id', 'kondisi', 'jumlah', 'foto',
    ];

    public function kategori()         { return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id'); }
    public function ruangan()          { return $this->belongsTo(Ruangan::class); }
    public function peminjamans()      { return $this->hasMany(Peminjaman::class); }
    public function laporanKerusakans() { return $this->hasMany(LaporanKerusakan::class); }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . ltrim($this->foto, '/')) : null;
    }

    /**
     * Sisa stok pada rentang waktu tertentu,
     * dihitung dari peminjaman berstatus "disetujui" yang jadwalnya beririsan.
     */
    public function stokTersedia(\DateTimeInterface|string $mulai, \DateTimeInterface|string $selesai, ?int $kecualiId = null): int
    {
        $terpakai = $this->peminjamans()
            ->where('status', 'disetujui')
            ->when($kecualiId, fn ($q) => $q->where('id', '!=', $kecualiId))
            ->where('tgl_pinjam', '<', $selesai)
            ->where('tgl_kembali', '>', $mulai)
            ->sum('jumlah');

        return max(0, $this->jumlah - $terpakai);
    }
}