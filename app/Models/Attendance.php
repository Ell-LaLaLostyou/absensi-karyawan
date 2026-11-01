<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'masuk_at' => 'datetime',
        'pulang_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}