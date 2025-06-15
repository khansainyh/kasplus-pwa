<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kategori_id',
        'deskripsi',
        // Pastikan kolom lain yang mungkin ada di tabel 'produks' juga ditambahkan di sini
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function variasis()
    {
        return $this->hasMany(Variasi::class);
    }
}