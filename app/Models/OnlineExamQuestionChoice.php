<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OnlineExamQuestionChoice
 *
 * @property int $id
 * @property int $online_exam_id
 * @property int $question_id
 * @property int|null $marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\OnlineExamQuestion $questions
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlineExamQuestionChoice extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function online_exam() {
        return $this->belongsTo(OnlineExam::class,'online_exam_id');
    }
    public function questions() {
        return $this->belongsTo(OnlineExamQuestion::class,'question_id')->with('options','answers');
    }
}
