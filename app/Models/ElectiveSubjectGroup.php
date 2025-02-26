<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ElectiveSubjectGroup
 *
 * @property int $id
 * @property int $total_subjects
 * @property int $total_selectable_subjects
 * @property int $class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $electiveSubjects
 * @property-read int|null $elective_subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSelectableSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ElectiveSubjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_subjects',
        'total_selectable_subjects',
        'class_id',
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function electiveSubjects() {
        return $this->hasMany(ClassSubject::class, 'elective_subject_group_id');
    }
}
