<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coffee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama kopi (11 macam)
            $table->enum('category', ['robusta', 'arabika']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coffee_types');
    }
};
