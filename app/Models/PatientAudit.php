<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAudit extends Model
{
    protected $table = 'patient_audits';
    protected $fillable = [
        'user_id',
        'patient_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
    
    /**
     * Get the user who performed the action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the patient associated with the audit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
