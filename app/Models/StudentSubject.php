<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StudentSubject
 *
 * @property int $id
 * @property int $student_id
 * @property int $subject_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentSubject extends Model
{
    use HasFactory;
    protected $fillable = ['student_id'];
    protected $hidden = ["deleted_at","created_at","updated_at"];
    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function scopeCurrentSessionYear($query){
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
