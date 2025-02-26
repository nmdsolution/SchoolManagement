<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo;
use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ExamClassSection
 *
 * @property int $id
 * @property int $exam_id
 * @property int|null $class_id
 * @property int|null $class_section_id
 * @property int $publish 0 => No, 1 => Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read mixed $center_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetable
 * @property-read int|null $timetable_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetableByClassID
 * @property-read int|null $timetable_by_class_i_d_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetableByExamID
 * @property-read int|null $timetable_by_exam_i_d_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection wherePublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamClassSection extends Model {
    use HasFactory;
    use Compoships;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $fillable = [
        'exam_id',
        'class_section_id',
        'publish'
    ];

    public function class_section() {
        return $this->belongsTo(ClassSection::class);
    }

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function timetableByClassID(): HasMany {
        return $this->hasMany(ExamTimetable::class, 'class_section_id', 'class_section_id');
    }

    public function class_timetable($exam_id = NULL, $class_id = NULL): HasMany {
        return $this->hasMany(ExamTimetable::class, ['class_section_id', 'exam_id'], ['class_section_id', 'exam_id']);
    }

    public function timetableByExamID(): HasMany {
        return $this->hasMany(ExamTimetable::class, 'exam_id', 'exam_id');
    }

    public function timetable() {
        return $this->hasMany(ExamTimetable::class, 'class_section_id', 'class_section_id');
    }

    /**
     * @return void
     * If user is the owner of Class Section
     */
    public function scopeOwner($query, $center_id = null) {
        if (Auth::user()->hasRole('Teacher')) {
            $center_id = $center_id ?? get_center_id();
            if (!is_array($center_id)) {
                $center_id = (array)$center_id;
            }
            $query = $query->whereHas('exam', function ($q) use ($center_id) {
                $q->owner()->whereIn('center_id', $center_id)->where('teacher_status', 1);
            });
        }
        if (Auth::user()->hasRole('Center')) {
            $query = $query->whereHas('exam', function ($q) {
                $q->where('center_id', Auth::user()->center->id);
            });
        }
        return $query;
    }

    public function getCenterNameAttribute() {
        $center_id = $this->class_section->class->center_id;
        $center = Center::select('name')->find($center_id);
        return $center->name;
    }
}
