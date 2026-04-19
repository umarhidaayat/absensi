<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    // Kolom-kolom di tabel shifts yang boleh diisi melalui form
    protected $fillable = [
        'office_id', 
        'name', 
        'start_time', 
        'end_time'
    ];

    // Relasi Belongs-To: 1 Shift dimiliki oleh 1 Kantor
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}