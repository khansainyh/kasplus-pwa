<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produks', function (Blueprint $table) {
            // Tambahkan kolom 'status' setelah kolom 'nama_kategori' (sesuaikan jika perlu)
            // Default 'tersedia' berarti semua produk lama Anda akan otomatis dianggap tersedia.
            $table->string('status')->default('tersedia')->after('kategori_id');
        });
    }

    public function down()
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};