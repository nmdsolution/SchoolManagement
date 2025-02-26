<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ExamResultGroupSubject
 *
 * @property int $id
 * @property int $subject_id
 * @property int $class_id
 * @property int|null $exam_result_group_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\ExamResultGroup|null $group
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereExamResultGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamResultGroupSubject extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'subject_id',
        'class_id',
        'exam_result_group_id',
        'center_id',
        'created_at',
        'updated_at'
    ];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function group()
    {
        return $this->belongsTo(ExamResultGroup::class, 'exam_result_group_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            $query = $query->where('center_id', Auth::user()->center->id);
        }
        return $query;
    }
}
