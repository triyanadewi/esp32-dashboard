<?php

use Illuminate\Support\Facades\Route;
use App\Models\SensorData;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/api/latest', function () {
    return [
        'latest' => SensorData::latest()->first(),
        'history' => SensorData::orderBy('created_at', 'desc')->take(20)->get()->reverse()->values(),
    ];
});