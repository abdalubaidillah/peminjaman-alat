<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogAktivitasResource;
use App\Models\LogAktivitas;
use Illuminate\Http\JsonResponse;

class LogAktivitasController extends Controller
{
    public function index(): JsonResponse
    {
        $logAktivitas = LogAktivitas::with('user')->latest()->get();

        return response()->json([
            'message' => 'Daftar log aktivitas berhasil diambil.',
            'data' => LogAktivitasResource::collection($logAktivitas),
        ]);
    }
}