<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OnlineExam
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property int|null $class_subject_id
 * @property string|null $title
 * @property int|null $exam_key
 * @property int|null $duration in minutes
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSubject|null $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionChoice> $question_choice
 * @property-read int|null $question_choice_count
 * @property-read \App\Models\StudentOnlineExamStatus|null $student_attempt
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereExamKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withoutTrashed()
 * @mixin \Eloquent
 */
class OnlineExam extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id')->with('class.medium', 'subject');
    }

    public function question_choice()
    {
        return $this->hasMany(OnlineExamQuestionChoice::class, 'online_exam_id');
    }

    public function student_attempt()
    {
        return $this->hasOne(StudentOnlineExamStatus::class, 'online_exam_id');
    }

}
