<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function regions(): JsonResponse
    {
        return response()->json(
            DB::table('regions')
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function provinces(Request $request): JsonResponse
    {
        $request->validate([
            'region_id' => ['required'],
        ]);

        return response()->json(
            DB::table('provinces')
                ->where('region_id', $request->region_id)
                ->orderBy('name')
                ->get(['id', 'name', 'region_id'])
        );
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => ['required'],
        ]);

        return response()->json(
            DB::table('cities')
                ->where('province_id', $request->province_id)
                ->orderBy('name')
                ->get(['id', 'name', 'province_id'])
        );
    }

    public function barangays(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => ['required'],
        ]);

        return response()->json(
            DB::table('barangays')
                ->where('city_id', $request->city_id)
                ->orderBy('name')
                ->get(['id', 'name', 'city_id'])
        );
    }
}
