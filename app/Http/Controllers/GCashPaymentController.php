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
                    'amount'   => $amountInCentavos,
                    'redirect' => [
                        'success' => route('gcash.success'),
                        'failed'  => route('gcash.failed'),
                    ],
                    'type'     => 'gcash',
                    'currency' => 'PHP',
                ]
            ]
        ]);

    $data = $response->json();

    // Store source ID and organization in session
    if (isset($data['data']['id'])) {
        session([
            'gcash_source_id' => $data['data']['id'],
            'gcash_organization' => $request->organization,
        ]);
    }

    $ID = session('gcash_source_id');

    Log::info('GCash source created', [
        'source_id' => $ID,
        'amount'    => $request->amount,
        'organization' => $request->organization,
    ]);

    // Log the full API response for debugging
    Log::info('PayMongo createSource Response:', $data);

    if (isset($data['data']['attributes']['redirect']['checkout_url'])) {
        return redirect($data['data']['attributes']['redirect']['checkout_url']);
    }

    return back()->withErrors(['gcash_error' => 'Unable to create GCash source.']);
}

}
