<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code_id',
        'standard_barcode_id',
        'short_description',
        'standard_description',
        'generic_name',
        'specifications',
        'item_category',
        'examination_type',
    ];

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_service_type');
    }
}
