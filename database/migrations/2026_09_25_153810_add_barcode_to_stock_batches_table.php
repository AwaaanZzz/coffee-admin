<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\StockBatch;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->string('barcode', 64)->nullable()->after('coffee_type_id');
        });

        // Migrate existing batches:
        // Set barcode = the existing 13-digit EAN-13
        // Set kode_produksi = clean batch production code (e.g. PRD-2609-001, etc.)
        $batches = DB::table('stock_batches')->orderBy('id')->get();
        $counter = 1;
        foreach ($batches as $b) {
            $existing = trim($b->kode_produksi ?? '');
            $barcodeVal = preg_match('/^899\d{10}$/', $existing) ? $existing : null;
            if (!$barcodeVal) {
                $barcodeVal = StockBatch::generateUniqueBarcode();
            }

            $dateYM = !empty($b->tgl_stock) ? date('ym', strtotime($b->tgl_stock)) : date('ym');
            $customKode = sprintf("PRD-%s-%03d", $dateYM, $counter++);

            DB::table('stock_batches')->where('id', $b->id)->update([
                'barcode' => $barcodeVal,
                'kode_produksi' => $customKode,
            ]);
        }

        // Add index on barcode
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropColumn('barcode');
        });
    }
};
