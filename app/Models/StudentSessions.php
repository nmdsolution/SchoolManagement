<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StudentSessions
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property int $result 1=>Pass,0=>fail
 * @property int $status 1=>continue,0=>leave
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $repeater
 * @property int $active 1=>Active,0=>Dismissed
 * @property int $promoted 1=>promoted,0=>created
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\SessionYear $sessionYear
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions wherePromoted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereRepeater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentSessions extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_section_id',
        'session_year_id',
        'result',
        'status',
        'promoted',
        'active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function student() {
        return $this->belongsTo(Students::class);
    }

    /**
     * Get the class_section that owns the StudentSessions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_section()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year'])->first();
    }
}
