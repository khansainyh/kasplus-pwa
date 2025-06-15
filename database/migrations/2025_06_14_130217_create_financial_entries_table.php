<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal entry
            $table->enum('tipe', ['pemasukan', 'pengeluaran']); // Jenis entry
            $table->decimal('jumlah', 15, 2); // Jumlah uang, pastikan presisi cukup
            $table->string('keterangan'); // Deskripsi/Keterangan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang menginput
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_entries');
    }
};
