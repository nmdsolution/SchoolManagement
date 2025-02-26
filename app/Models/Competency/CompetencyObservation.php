<?php

namespace App\Models\Competency;

use App\Models\ExamTerm;
use App\Models\Students;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * App\Models\Competency\CompetencyObservation
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_term_id
 * @property string|null $teacher_signature
 * @property string|null $director_signature
 * @property string|null $parent_signature
 * @property string $observation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency\CouncilReview|null $council_review
 * @property-read ExamTerm $exam_term
 * @property-read Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereDirectorSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereObservation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereParentSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereTeacherSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyObservation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompetencyObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_term_id',
        'observation',
        'teacher_signature',
        'director_signature',
        'parent_signature'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    public function exam_term()
    {
        return $this->belongsTo(ExamTerm::class);
    }

    public function council_review()
    {
        return $this->hasOne(CouncilReview::class);
    }
}
