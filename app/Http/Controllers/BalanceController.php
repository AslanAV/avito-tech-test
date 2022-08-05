<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'count' => 'required|numeric|gt:0',
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

        $balance->balance += $request->get('count');

        $balance->save();

        return response()->json($balance);
    }

    public function writeOff(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'count' => 'required|numeric|gt:0',
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

        $balance->balance -= $request->get('count');

        if ($balance->balance < 0) {
            return response()->json([
                'message' => __('Недостаточно средств')
            ], 400);
        }

        $balance->save();

        return response()->json($balance);
    }
}