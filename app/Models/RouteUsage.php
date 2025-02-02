<?php
// app/Models/RouteUsage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'origin',
        'destination',
        'route_type',
        'route_name',
        'color',
        'frequency',
    ];

    protected $casts = [
        'route_type' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}