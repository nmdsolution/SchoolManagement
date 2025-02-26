<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Matrix\Builder;

/**
 * App\Models\ExamSequence
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $name
 * @property int $exam_term_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $exam_created
 * @property int $status
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoSequenceExam> $auto_sequence_exam
 * @property-read int|null $auto_sequence_exam_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $auto_sequence_exam_class_section
 * @property-read int|null $auto_sequence_exam_class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exam
 * @property-read int|null $exam_count
 * @property-read \App\Models\ExamTerm $term
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereExamCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamSequence extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'exam_term_id',
        'start_date',
        'end_date',
        'status',
        'center_id',
    ];

    public function term() {
        return $this->belongsTo(ExamTerm::class, 'exam_term_id');
    }

    public function exam() {
        return $this->hasMany(Exam::class, 'exam_sequence_id');
    }

    public function auto_sequence_exam() {
        return $this->hasMany(AutoSequenceExam::class);
    }

    public function auto_sequence_exam_class_section() {
        return $this->belongsToMany(ClassSection::class, AutoSequenceExam::class, 'exam_sequence_id', 'class_section_id');
    }

    public function scopeOwner($query, $center_id = null) {
        if (Auth::user()->hasRole('Center')) {
            $query = $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher') && session()->get('center_id')) {
            $query = $query->where('center_id', session()->get('center_id'));
        }
        return $query;
    }
}
