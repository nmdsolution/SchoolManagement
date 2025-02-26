<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubjectCompetency
 *
 * @property int $id
 * @property int $exam_sequence_id
 * @property int $exam_id
 * @property int $subject_id
 * @property int $class_section_id
 * @property string $competence
 * @property-read \App\Models\ExamClassSection $classSection
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\ExamTerm $exam_term
 * @property-read \App\Models\ExamSequence $sequence
 * @property-read \App\Models\ClassSubject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereCompetence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectCompetency whereSubjectId($value)
 * @mixin \Eloquent
 */
class SubjectCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_sequence_id',
        'exam_id',
        'subject_id',
        'class_section_id',
        'competence'
    ];

    public $timestamps = false;
    public function sequence()
    {
        return $this->belongsTo(ExamSequence::class, 'exam_sequence_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function exam_term()
    {
        return $this->belongsTo(ExamTerm::class);
    }

    public function subject()
    {
        return $this->belongsTo(ClassSubject::class, 'subject_id');
    }

    public function classSection()
    {
        return $this->belongsTo(ExamClassSection::class, 'class_section_id');
    }
}
