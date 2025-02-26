<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OnlineExamQuestion
 *
 * @property int $id
 * @property int $class_subject_id
 * @property int $question_type 0 - simple 1 - equation based
 * @property string $question
 * @property string|null $image_url
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionOption> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlineExamQuestion extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function class_subject() {
        return $this->belongsTo(ClassSubject::class,'class_subject_id');
    }
    public function options(){
        return $this->hasMany(OnlineExamQuestionOption::class,'question_id');
    }
    public function answers(){
        return $this->hasMany(OnlineExamQuestionAnswer::class,'question_id')->with('options');
    }

    public function getImageUrlAttribute($value) {
        if($value){
            return url(Storage::url($value));
        }else{
            return null;
        }
    }
}
