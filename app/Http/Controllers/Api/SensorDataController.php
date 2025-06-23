<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'gas' => 'required|integer',
        ]);

        $data = SensorData::create($validated);

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
