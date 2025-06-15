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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Relasi ke transaksi utama
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade'); // Relasi ke produk
            $table->foreignId('variasi_id')->nullable()->constrained('variasis')->onDelete('cascade'); // Relasi ke variasi (bisa null)
            $table->string('item_name'); // Nama produk/variasi saat transaksi (untuk histori)
            $table->decimal('item_price', 15, 2); // Harga produk/variasi saat transaksi
            $table->integer('quantity'); // Kuantitas
            $table->decimal('subtotal', 15, 2); // Harga * kuantitas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};