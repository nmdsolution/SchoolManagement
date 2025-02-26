<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OnlineExamQuestionOption
 *
 * @property int $id
 * @property int $question_id
 * @property string $option
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnlineExamQuestionOption extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
}
