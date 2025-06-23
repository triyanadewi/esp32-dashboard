<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = SensorData::latest()->first();
        $history = SensorData::whereDate('created_at', today())
                    ->orderBy('created_at', 'asc')
                    ->limit(20)
                    ->get();

        return view('dashboard', compact('latest', 'history'));
    }
}
