<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->decimal('pemasukan', 14, 2)->default(0);
            $table->decimal('pengeluaran', 14, 2)->default(0);
            $table->decimal('laba', 14, 2)->default(0); // pemasukan - pengeluaran
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_reports');
    }
};
