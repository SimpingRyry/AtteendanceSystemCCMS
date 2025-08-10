<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GCashPaymentController extends Controller
{
public function createSource(Request $request)
{
    $request->validate([
        'amount'       => 'required|numeric|min:20',
        'organization' => 'required|string',
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
                        // Pass org back to success route
                        'success' => route('gcash.success', ['organization' => $request->organization]),
                        'failed'  => route('gcash.failed'),
                    ],
                    'type'     => 'gcash',
                    'currency' => 'PHP'
                ]
            ]
        ]);

    if ($response->failed()) {
        $errorMessage = $response->json('errors.0.detail') ?? 'Failed to initiate GCash payment.';
        return back()->withInput()->with('error', $errorMessage);
    }

    $source = $response->json();
    return redirect($source['data']['attributes']['redirect']['checkout_url']);
}
}
