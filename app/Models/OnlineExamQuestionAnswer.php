<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OnlineExamQuestionAnswer
 *
 * @property int $id
 * @property int $question_id
 * @property int $answer option id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExamQuestionOption $options
 * @property-read \App\Models\OnlineExamQuestionOption $question_option
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlineExamQuestionAnswer extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function question_option() {
        return $this->belongsTo(OnlineExamQuestionOption::class,'answer');
    }
    public function options() {
        return $this->belongsTo(OnlineExamQuestionOption::class,'answer');
    }
}
