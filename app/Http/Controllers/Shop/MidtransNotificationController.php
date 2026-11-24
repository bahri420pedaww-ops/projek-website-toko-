<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

/**
 * Webhook Midtrans (dipanggil oleh server Midtrans, bukan oleh pembeli).
 * Keasliannya dicek lewat signature, bukan lewat login atau CSRF.
 *
 * Kode respons penting: 2xx = diterima (Midtrans tidak mengulang), 5xx = Midtrans mencoba lagi.
 */
class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments)
    {
        try {
            $payments->handleNotification($request->all());
        } catch (InvalidArgumentException $e) {
            Log::warning('Notifikasi Midtrans ditolak: ' . $e->getMessage());

            return response()->json(['message' => 'Invalid notification'], 403);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Server error'], 500);
        }

        return response()->json(['message' => 'OK']);
    }
}
