<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class GCashPaymentController extends Controller
{
    public function createSource()
    {
        $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post('https://api.paymongo.com/v1/sources', [
                'data' => [
                    'attributes' => [
                        'amount' => 10000, // PHP 100.00 in centavos
                        'redirect' => [
                            'success' => route('gcash.success'),
                            'failed' => route('gcash.failed'),
                        ],
                        'type' => 'gcash',
                        'currency' => 'PHP'
                    ]
                ]
            ]);

        $source = $response->json();
        return redirect($source['data']['attributes']['redirect']['checkout_url']);
    }
}
