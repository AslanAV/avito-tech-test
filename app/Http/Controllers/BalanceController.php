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

    public function show(User $user): JsonResponse
    {
        $balance = Balance::firstOrNew([
            'user_id' => $user->id,
        ]);

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
}