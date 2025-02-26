<?php

namespace App\Models\Competency;

use App\Models\ExamTerm;
use App\Models\ClassSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\LearningUnit
 *
 * @property-read ClassSchool $class
 * @property-read ExamTerm $exam_term
 * @method static \Illuminate\Database\Eloquent\Builder|LearningUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LearningUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LearningUnit query()
 * @mixin \Eloquent
 */
class LearningUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'exam_term_id',
        'class_id',
    ];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }

    public function exam_term()
    {
        return $this->belongsTo(ExamTerm::class);
    }
}
