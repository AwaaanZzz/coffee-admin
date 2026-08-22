<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coffee_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_batch_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga', 12, 2); // harga saat transaksi
            $table->decimal('total', 14, 2); // jumlah x harga
            $table->date('tanggal');
            $table->timestamps();

            $table->index(['store_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
