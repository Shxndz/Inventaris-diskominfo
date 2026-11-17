<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LaporanKerusakan extends Model
{
    protected $table = 'laporan_kerusakans';

    protected $fillable = [
        'user_id', 'barang_id', 'ruangan_id', 'deskripsi',
        'foto', 'status', 'catatan_admin', 'ditangani_oleh',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function barang()   { return $this->belongsTo(Barang::class); }
    public function ruangan()  { return $this->belongsTo(Ruangan::class); }
    public function penangan() { return $this->belongsTo(User::class, 'ditangani_oleh'); }

    public function getFotoUrlAttribute(): ?string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $this->foto ? $disk->url($this->foto) : null;
    }
}