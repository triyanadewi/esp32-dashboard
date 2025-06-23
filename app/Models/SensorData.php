<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SensorData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sensor_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'temperature',
        'humidity',
        'gas',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'float',
        'gas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the latest sensor reading.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function getLatest()
    {
        return static::latest()->first();
    }

    /**
     * Get sensor data within a specific time range.
     *
     * @param string $from
     * @param string $to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDataBetween($from, $to)
    {
        return static::whereBetween('created_at', [$from, $to])->orderBy('created_at')->get();
    }

    /**
     * Get sensor data for today.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTodayData()
    {
        return static::whereDate('created_at', today())->orderBy('created_at')->get();
    }
}
