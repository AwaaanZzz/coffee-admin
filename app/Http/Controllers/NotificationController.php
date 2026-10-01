<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(?Request $request = null)
    {
        $request = $request ?? request();
        self::syncOperationalAlerts();

        $query = Notification::query();

        if ($request->get('filter') === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = Notification::where('is_read', false)->count();
        $totalCount = Notification::count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount'));
    }

    public function go(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return redirect($notification->safe_link);
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        $unreadCount = Notification::where('is_read', false)->count();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        $unreadCount = Notification::where('is_read', false)->count();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function clearAll()
    {
        Notification::truncate();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->route('notifications.index')->with('success', 'Semua riwayat notifikasi dibersihkan.');
    }

    /**
     * Sinkronisasi alert operasional otomatis:
     * 1. Stok Kadaluarsa (Expired) & masih ada sisa
     * 2. Stok Mendekati Kadaluarsa (<= 7 hari)
     * 3. Stok Menipis Kritis (<= 2 pcs)
     */
    public static function syncOperationalAlerts(): void
    {
        try {
            // 1. Alert Stok Sudah Kadaluarsa (Expired)
            $expiredBatches = StockBatch::with(['store', 'coffeeType'])
                ->where('status', '!=', 'tarik')
                ->whereRaw('(jumlah_stock - laku) > 0')
                ->where('tgl_exp', '<', now()->startOfDay())
                ->limit(10)
                ->get();

            foreach ($expiredBatches as $batch) {
                $coffeeName = $batch->coffeeType->name ?? 'Kopi';
                $storeName = $batch->store->name ?? 'Toko';
                $code = $batch->kode_produksi ?: ($batch->barcode ?: 'Batch #' . $batch->id);
                $title = "KADALUARSA: {$coffeeName} ({$code})";
                $message = "Batch [{$code}] di {$storeName} telah expired pada " . ($batch->tgl_exp ? $batch->tgl_exp->format('d/m/Y') : '-') . ". Sisa {$batch->sisa} pcs perlu ditarik segera!";
                $link = '/stock?search=' . urlencode($code);

                $exists = Notification::where('title', $title)
                    ->where('created_at', '>=', now()->subDays(2))
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'title' => $title,
                        'message' => $message,
                        'type' => 'danger',
                        'icon' => 'alert-octagon',
                        'link' => $link,
                        'is_read' => false,
                    ]);
                }
            }

            // 2. Alert Stok Mendekati Kadaluarsa (<= 7 hari ke depan)
            $expiringBatches = StockBatch::with(['store', 'coffeeType'])
                ->where('status', '!=', 'tarik')
                ->whereRaw('(jumlah_stock - laku) > 0')
                ->whereBetween('tgl_exp', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->limit(10)
                ->get();

            foreach ($expiringBatches as $batch) {
                $coffeeName = $batch->coffeeType->name ?? 'Kopi';
                $storeName = $batch->store->name ?? 'Toko';
                $code = $batch->kode_produksi ?: ($batch->barcode ?: 'Batch #' . $batch->id);
                $title = "Mendekati Kadaluarsa: {$coffeeName} ({$code})";
                $message = "Batch [{$code}] di {$storeName} exp pada " . ($batch->tgl_exp ? $batch->tgl_exp->format('d/m/Y') : '-') . ". Sisa {$batch->sisa} pcs.";
                $link = '/stock?search=' . urlencode($code);

                $exists = Notification::where('title', $title)
                    ->where('created_at', '>=', now()->subDays(2))
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'title' => $title,
                        'message' => $message,
                        'type' => 'warning',
                        'icon' => 'alert-triangle',
                        'link' => $link,
                        'is_read' => false,
                    ]);
                }
            }

            // 3. Alert Stok Menipis Kritis (<= 2 pcs)
            $lowStockBatches = StockBatch::with(['store', 'coffeeType'])
                ->where('status', '!=', 'tarik')
                ->whereRaw('(jumlah_stock - laku) > 0 AND (jumlah_stock - laku) <= 2')
                ->limit(10)
                ->get();

            foreach ($lowStockBatches as $batch) {
                $coffeeName = $batch->coffeeType->name ?? 'Kopi';
                $storeName = $batch->store->name ?? 'Toko';
                $code = $batch->kode_produksi ?: ($batch->barcode ?: 'Batch #' . $batch->id);
                $title = "Stok Kritis: {$coffeeName} ({$storeName})";
                $message = "Sisa hanya {$batch->sisa} pcs pada batch [{$code}]. Pertimbangkan kirim stok tambahan ke {$storeName}.";
                $link = '/stock?store_id=' . $batch->store_id;

                $exists = Notification::where('title', $title)
                    ->where('created_at', '>=', now()->subDays(2))
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'title' => $title,
                        'message' => $message,
                        'type' => 'danger',
                        'icon' => 'package-x',
                        'link' => $link,
                        'is_read' => false,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently handle exceptions so page render is never interrupted
        }
    }
}
