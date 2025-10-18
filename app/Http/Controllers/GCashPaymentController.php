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
        'amount'          => 'required|numeric|min:20',
        'selected_events' => 'required|json', // from hidden input
    ], [
        'amount.min' => 'Minimum payment amount is ₱20.00 due to GCash restrictions.'
    ]);

    $amountInCentavos = $request->amount * 100;
    $selectedEvents = json_decode($request->selected_events, true);

    if (empty($selectedEvents) || !is_array($selectedEvents)) {
        return back()->withErrors(['selected_events' => 'No events selected for payment.']);
    }

    // ✅ Create PayMongo GCash source
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

    // ✅ Store source ID and selected events in session
    if (isset($data['data']['id'])) {
        session([
            'gcash_source_id' => $data['data']['id'],
            'gcash_selected_events' => $selectedEvents,
            'gcash_amount' => $request->amount,
        ]);
    }

    Log::info('GCash source created', [
        'source_id' => session('gcash_source_id'),
        'amount' => $request->amount,
        'selected_events' => $selectedEvents,
    ]);

    // Redirect user to GCash checkout
    if (isset($data['data']['attributes']['redirect']['checkout_url'])) {
        return redirect($data['data']['attributes']['redirect']['checkout_url']);
    }

    return back()->withErrors(['gcash_error' => 'Unable to create GCash source. Please try again.']);
}

}
