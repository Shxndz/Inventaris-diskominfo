<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $fillable = ['nama', 'lokasi'];

    public function barangs() { return $this->hasMany(Barang::class); }
}
