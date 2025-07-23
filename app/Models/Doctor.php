<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'specialization',
        'phone',
        'email',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
    
    /**
     * Get the patients associated with the doctor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'appointments', 'doctor_id', 'patient_id');
    }

}
