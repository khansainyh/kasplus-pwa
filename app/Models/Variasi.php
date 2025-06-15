<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'nama',
        'harga',
        'stok',
        // Pastikan kolom lain yang mungkin ada di tabel 'variasis' juga ditambahkan di sini
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}