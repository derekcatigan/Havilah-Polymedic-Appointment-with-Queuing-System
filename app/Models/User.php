<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'doctor_user_id',
        'patient_id',
        'patient_number',
        'name',
        'email',
        'role',
        'contact_number',
        'address',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function doctor()
    {
        return $this->hasOne(DoctorProfile::class, 'user_id');
    }

    public function appointmentsAsDoctor()
    {
        return $this->hasMany(Appointment::class, 'doctor_user_id');
    }

    public function serviceTypes()
    {
        return $this->hasManyThrough(ServiceType::class, Appointment::class, 'patient_user_id', 'id', 'id', 'service_type_id');
    }

    public function appointmentsAsPatient()
    {
        return $this->hasMany(Appointment::class, 'patient_user_id');
    }

    public function queuesAsDoctor()
    {
        return $this->hasMany(Queue::class, 'doctor_user_id');
    }

    public function queuesAsPatient()
    {
        return $this->hasMany(Queue::class, 'patient_user_id');
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_user_id');
    }
}
