<?php

namespace App\Models;

use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\SessionYear;
use App\Models\ClassSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StudentAttendance
 *
 * @property int $id
 * @property int $exam_term_id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $session_year_id
 * @property int $total_absences
 * @property int $justified_absences
 * @property int $unjustified_absences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ClassSection $class_section
 * @property-read ExamTerm $exam_term
 * @property-read SessionYear $session_year
 * @property-read Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereJustifiedAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereTotalAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereUnjustifiedAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_term_id', 'class_section_id', 'student_id', 
        'session_year_id', 'total_absences', 'justified_absences', 'unjustified_absences'
    ];

    public function exam_term(){
        return $this->belongsTo(ExamTerm::class);
    }

    public function session_year(){
        return $this->belongsTo(SessionYear::class);
    }

    public function student(){
        return $this->belongsTo(Students::class);
    }

    public function class_section(){
        return $this->belongsTo(ClassSection::class);
    }

    public function scopeCurrentSessionYear($query){
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
