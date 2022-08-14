<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function add(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'count' => 'required|numeric|gt:0',
        ]);

        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

        $balance->balance += $request->get('count');

        $balance->save();

        return response()->json($balance);
    }

    public function writeOff(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'count' => 'required|numeric|gt:0',
        ]);

        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

        $balance->balance -= $request->get('count');

        if ($balance->balance < 0) {
            return response()->json([
                'message' => __('Insufficient funds.'),
            ], 400);
        }

        $balance->save();

        return response()->json($balance);
    }

    public function show(User $user, Request $request): JsonResponse
    {
        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

        $input = $request->all();
        if (array_key_exists('currency', $input)) {
            $currency = $input['currency'];
            $balanceUser = $input('balance');
            $result = $this->getAnotherCurrency($currency, $balance);
            $balance->$balance = $result['info']['rate'];
        }

        return response()->json($balance);
    }

    public function transaction(Request $request, User $user1, User $user2): JsonResponse
    {
        $request->validate([
            'count' => 'required|numeric|gt:0',
        ]);

        $balance1 = Balance::firstOrNew([
            'user_id' => $user1->id,
        ]);

        $count = $request->get('count');

        $balance1->balance -= $count;

        if ($balance1->balance < 0) {
            return response()->json([
                'message' => __('Insufficient funds from user1'),
            ], 400);
        }

        $balance1->save();

        $balance2 = Balance::firstOrNew([
            'user_id' => $user2->id,
        ]);

        $balance2->balance += $count;

        $balance2->save();

        return response()->json([$balance1, $balance2]);
    }

    public function getAnotherCurrency(string $currency, int $amount): bool|array|string
    {
        $apikey = env('API_TOKEN_APILAYER', false);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/convert?to={$currency}&from=rub&amount={$amount}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/plain",
                "apikey: {$apikey}"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        if (!$response) {
            return [
                "result" => $amount,
                'query' => ["from" => $currency],
                'message' => 'сервис конвертации не доступен'
            ];
        }
        return $response;
    }


}