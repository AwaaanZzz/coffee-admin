<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->is_read = true;
            $notification->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function markAllRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public static function createStockAlerts()
    {
        $expiringStocks = StockBatch::where('tgl_exp', '<=', now()->addDays(7))
            ->where('jumlah_stock', '>', 0)
            ->get();

        foreach ($expiringStocks as $stock) {
            // Logic to create notification for admins
            // This is a placeholder since Notification model schema might differ
            /*
            Notification::create([
                'user_id' => 1, // Or loop admin users
                'title' => 'Stok Hampir Kadaluarsa',
                'message' => 'Batch ' . $stock->kode_produksi . ' akan kadaluarsa pada ' . $stock->tgl_exp,
                'type' => 'warning',
                'is_read' => false
            ]);
            */
        }
    }
}
