<?php
// app/Models/CustomRoute.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'transport_type', 'waypoints', 'color', 'fare', 'duration'
    ];

    protected $casts = [
        'waypoints' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}