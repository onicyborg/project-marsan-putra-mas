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
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable(); // bisa null kalau bukan member
            $table->string('customer_name')->nullable(); // nama customer manual kalau user_id kosong

            $table->string('order_id')->unique()->nullable(); // order id midtrans
            $table->string('payment_type')->nullable(); // bank_transfer, qris, ewallet, dll
            $table->string('payment_code')->nullable(); // VA number, QR code, dsb
            $table->string('transaction_id')->nullable(); // id transaksi dari midtrans

            $table->decimal('gross_amount', 15, 2)->default(0); // jumlah pembayaran
            $table->enum('status', ['Pending', 'Success', 'Expire', 'Cancel', 'Failed'])->default('Pending');
            $table->enum('payment_method', ['Cash', 'Transfer'])->nullable();

            $table->timestamp('transaction_time')->nullable(); // waktu transaksi midtrans
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
