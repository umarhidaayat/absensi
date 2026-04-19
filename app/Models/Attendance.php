<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar kolom bisa diisi via form
    protected $fillable = [
        'user_id', 
        'date', 
        'time_in', 
        'time_out', 
        'location', 
        'status'
    ];

    // Relasi ke User (Nanti kita pakai)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}