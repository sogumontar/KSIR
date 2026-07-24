<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaundryReceiptController extends Controller
{
    public function download($id)
    {
        $order = LaundryOrder::with(['items.service', 'user.merchantSetting', 'promo'])->findOrFail($id);
        
        // Basic auth check for admin/owner of order
        if (!Auth::check() || Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $pdf = Pdf::loadView('pdf.laundry-receipt', compact('order'));
        // Set paper size to 80mm width (approx 226.77 pt) thermal receipt paper
        $pdf->setPaper([0, 0, 226.77, 800]);

        return $pdf->download("receipt-{$order->order_code}.pdf");
    }
}
