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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor faktur/nota
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Kasir yang melakukan transaksi
            $table->decimal('total_amount', 15, 2); // Total belanja
            $table->string('payment_method'); // tunai, qris
            $table->decimal('amount_paid', 15, 2)->nullable(); // Uang diterima (hanya untuk tunai)
            $table->decimal('change_amount', 15, 2)->nullable(); // Jumlah kembalian (hanya untuk tunai)
            $table->timestamp('transaction_date'); // Tanggal dan waktu transaksi
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};