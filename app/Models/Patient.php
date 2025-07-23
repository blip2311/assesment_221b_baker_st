<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_details',
    ];
    protected $casts = [
        'insurance_details' => 'array',
        'date_of_birth' => 'date',
    ];
    
    /**
     * Get the user associated with the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
    
    /**
     * Get the appointments associated with the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'appointments', 'patient_id', 'doctor_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->patient_id = (string) Str::uuid();
        });
    }
}
