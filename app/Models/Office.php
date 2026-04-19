<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    // Perhatikan: shift_start dan shift_end sudah dihapus dari sini
    protected $fillable = [
        'name', 
        'latitude', 
        'longitude', 
        'radius'
    ];

    // Relasi One-to-Many: 1 Kantor memiliki Banyak Shift
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}