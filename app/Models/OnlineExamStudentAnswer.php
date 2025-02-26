<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OnlineExamStudentAnswer
 *
 * @property int $id
 * @property int $student_id
 * @property int $online_exam_id
 * @property int $question_id online exam question choice id
 * @property int $option_id
 * @property string $submitted_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereSubmittedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlineExamStudentAnswer extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function online_exam() {
        return $this->belongsTo(OnlineExam::class,'online_exam_id');
    }
}
