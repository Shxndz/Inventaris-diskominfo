<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id', 'barang_id', 'ruangan_id', 'jumlah', 'tgl_pinjam',
        'tgl_kembali', 'keperluan', 'status', 'catatan_admin',
        'disetujui_oleh', 'tgl_dikembalikan',
    ];

    protected $casts = [
        'tgl_pinjam' => 'datetime',
        'tgl_kembali' => 'datetime',
        'tgl_dikembalikan' => 'datetime',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function barang()    { return $this->belongsTo(Barang::class); }
    public function ruangan()   { return $this->belongsTo(Ruangan::class); }
    public function penyetuju() { return $this->belongsTo(User::class, 'disetujui_oleh'); }
}
