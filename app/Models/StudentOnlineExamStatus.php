<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StudentOnlineExamStatus
 *
 * @property int $id
 * @property int $student_id
 * @property int $online_exam_id
 * @property int $status 1 - in progress 2 - completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\Students $student_data
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentOnlineExamStatus extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function online_exam() {
        return $this->belongsTo(OnlineExam::class,'online_exam_id');
    }

    public function student_data() {
        return $this->belongsTo(Students::class,'student_id');
    }
}
