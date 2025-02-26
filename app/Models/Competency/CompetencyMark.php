<?php

namespace App\Models\Competency;

use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\SessionYear;
use App\Models\Students;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Competency\CompetencyMark
 *
 * @property int $id
 * @property int $student_id
 * @property int $competency_id
 * @property int $competency_type_id
 * @property int $exam_term_id
 * @property int $exam_sequence_id
 * @property int $session_year_id
 * @property float $obtained_marks
 * @property bool $passing_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Competency\Competency $competency
 * @property-read \App\Models\Competency\CompetencyType $competencyType
 * @property-read ExamSequence $sequence
 * @property-read SessionYear $sessionYear
 * @property-read Students $student
 * @property-read ExamTerm $term
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereCompetencyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark wherePassingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyMark withoutTrashed()
 * @mixin \Eloquent
 */
class CompetencyMark extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'competency_id',
        'competency_type_id',
        'exam_term_id',
        'exam_sequence_id',
        'session_year_id',
        'obtained_marks',
        'passing_status'
    ];

    protected $casts = [
        'passing_status' => 'boolean',
        'obtained_marks' => 'float'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function competencyType()
    {
        return $this->belongsTo(CompetencyType::class);
    }

    public function term()
    {
        return $this->belongsTo(ExamTerm::class, 'exam_term_id');
    }

    public function sequence()
    {
        return $this->belongsTo(ExamSequence::class, 'exam_sequence_id');
    }

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }
} 