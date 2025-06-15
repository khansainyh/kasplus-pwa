<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // Baris ini memberitahu Laravel bahwa HANYA kolom ini yang boleh diisi secara massal
    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}