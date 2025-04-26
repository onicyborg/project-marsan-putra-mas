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
        Schema::create('detail_transaction', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id'); // foreign key ke transactions
            $table->string('service_name');
            // $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0); // harga per item
            // $table->decimal('total_price', 15, 2)->default(0); // harga x qty

            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaction');
    }
};
