<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Initialize a payment and get authorization URL
     */
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100', // amount in kobo
        ]);

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->post(env('PAYSTACK_PAYMENT_URL') . '/transaction/initialize', [
                'email' => $validated['email'],
                'amount' => $validated['amount'] * 100, // convert to kobo
                'callback_url' => route('paystack.callback'),
            ]);

        return $response->json();
    }

    /**
     * Handle Paystack payment callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->get(env('PAYSTACK_PAYMENT_URL') . '/transaction/verify/' . $reference);

        $data = $response->json();

        if ($data['data']['status'] === 'success') {
            // ✅ You can record the payment here in your database
            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully',
                'data' => $data['data'],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Payment verification failed',
            'data' => $data['data'] ?? null,
        ]);
    }
}
