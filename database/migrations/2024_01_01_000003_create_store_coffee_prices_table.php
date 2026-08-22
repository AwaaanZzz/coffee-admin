<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_coffee_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coffee_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2); // harga per toko per jenis kopi
            $table->timestamps();

            $table->unique(['store_id', 'coffee_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_coffee_prices');
    }
};
