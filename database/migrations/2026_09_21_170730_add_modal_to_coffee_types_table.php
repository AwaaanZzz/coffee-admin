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
        Schema::table('coffee_types', function (Blueprint $table) {
            $table->decimal('modal', 14, 2)->default(0)->after('category'); // Modal HPP per pack/pcs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_types', function (Blueprint $table) {
            $table->dropColumn('modal');
        });
    }
};
