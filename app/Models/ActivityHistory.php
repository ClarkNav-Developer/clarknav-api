<?php
// filepath: /C:/Users/kenji/OneDrive/Pictures/clarknav-api/app/Models/ActivityHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}