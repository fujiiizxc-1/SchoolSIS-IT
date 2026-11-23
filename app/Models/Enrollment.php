<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    public const SCHOOL_YEAR = '2026-2027';

    public const SEMESTER = '1';

    protected $fillable = ['subject_id', 'school_year', 'semester'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }
}
