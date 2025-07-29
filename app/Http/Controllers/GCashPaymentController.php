<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GCashPaymentController extends Controller
{
public function createSource(Request $request)
{
    // Validate that user entered at least ₱20
    $request->validate([
        'amount' => 'required|numeric|min:20'
    ], [
        'amount.min' => 'Minimum payment amount is ₱20.00 due to GCash restrictions.'
    ]);

    $amountInCentavos = $request->amount * 100;

    $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
        ->post('https://api.paymongo.com/v1/sources', [
            'data' => [
                'attributes' => [
                    'amount' => (int)$amountInCentavos,
                    'redirect' => [
                        'success' => route('gcash.success'),
                        'failed' => route('gcash.failed'),
                    ],
                    'type' => 'gcash',
                    'currency' => 'PHP'
                ]
            ]
        ]);

    if ($response->failed()) {
        // Log the full error for debugging
        Log::error('GCash Payment Error', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        // Try to get the detailed error message from PayMongo response
        $errorMessage = $response->json('errors.0.detail') ?? 'Failed to initiate GCash payment.';

        // Redirect back with input and error for modal
        return back()->withInput()->with('error', $errorMessage);
    }

    $source = $response->json();

    return redirect($source['data']['attributes']['redirect']['checkout_url']);
}
}
