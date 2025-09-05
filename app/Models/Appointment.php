<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = "appointments";
    protected $fillable = [
        'doctor_user_id',
        'patient_user_id',
        'starts_at',
        'ends_at',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function queue()
    {
        return $this->hasOne(Queue::class, 'appointment_id');
    }
}
