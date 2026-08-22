<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coffee_type_id')->constrained()->cascadeOnDelete();
            $table->string('kode_produksi');
            $table->date('tgl_stock');
            $table->date('tgl_exp');
            $table->integer('jumlah_stock'); // stock masuk
            $table->integer('laku')->default(0); // terjual
            // sisa dihitung otomatis: jumlah_stock - laku (accessor di model)
            $table->enum('status', ['normal', 'tarik', 'ganti'])->default('normal');
            $table->timestamps();

            $table->index(['store_id', 'coffee_type_id']);
            $table->index('tgl_exp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
