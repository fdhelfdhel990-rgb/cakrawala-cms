<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of active promotional banners for the mobile home slider.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Banner::active();

        if ($request->filled('limit')) {
            $query->take((int) $request->query('limit'));
        }

        $banners = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data banner berhasil diambil',
            'data' => $banners,
        ]);
    }
}
