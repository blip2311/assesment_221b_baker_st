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
        // Automatically generate a UUID for the patient_id when creating a new patient
        static::creating(function ($model) {
            $model->patient_id = (string) Str::uuid();
        });

        // Create audit logs for create actions
        static::created(function ($model) {
            \App\Models\PatientAudit::create([
                'user_id' => auth()->id(),
                'patient_id' => $model->id,
                'action' => 'created',
                'old_values' => null,
                'new_values' => $model->getAttributes(),
                'ip_address' => request()->ip(),
            ]);
        });

        
        // Create audit logs for update actions
        static::updated(function ($model) {
            \App\Models\PatientAudit::create([
                'user_id' => auth()->id(),
                'patient_id' => $model->id,
                'action' => 'updated',
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getAttributes(),
                'ip_address' => request()->ip(),
            ]);
        });

        
        // Create audit logs for delete actions
        static::deleted(function ($model) {
            \App\Models\PatientAudit::create([
                'user_id' => auth()->id(),
                'patient_id' => $model->id,
                'action' => 'deleted',
                'old_values' => $model->getOriginal(),
                'new_values' => null,
                'ip_address' => request()->ip(),
            ]);
        });
    }
    public function audits()
    {
        return $this->hasMany(PatientAudit::class, 'patient_id');
    }
}
