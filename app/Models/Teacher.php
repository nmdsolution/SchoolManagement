<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Relations\HasMany as RelationsHasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Teacher
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $qualification
 * @property string|null $qualification_certificate
 * @property int|null $salary
 * @property int $contact_status 0 -> Not visible, 1 -> Visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CenterTeacher> $center_teacher
 * @property-read int|null $center_teacher_count
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_sections
 * @property-read int|null $class_sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $classes
 * @property-read int|null $classes_count
 * @property-read mixed $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher teachers()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereContactStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereQualificationCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withoutTrashed()
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'qualification',
        'salary'
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function announcement()
    {
        return $this->morphMany(Announcement::class, 'modal');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function class_section()
    {
        // Find the Class of the Class Teacher
        return $this->hasOne(ClassSection::class, 'class_teacher_id');
    }

    public function class_sections()
    {
        // Find the Multiple Classes of a Class Teacher
        return $this->hasMany(ClassSection::class, 'class_teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id');
    }

    public function classes()
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id')->groupBy('class_section_id');
    }

    public function scopeTeachers($query)
    {
        if (Auth::user()->hasRole('Teacher')) {
            return $query->where('user_id', Auth::user()->id);
        }

        return $query;
    }

    public function center_teacher()
    {
        return $this->hasMany(CenterTeacher::class);
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Teacher')) {
            return $query->where('user_id', Auth::user()->id);
        }

        if (Auth::user()->hasRole('Center')) {
            $centerTeacher = CenterTeacher::where('center_id', Auth::user()->center->id)->select('teacher_id')->pluck('teacher_id');
            return $query->whereIn('id', $centerTeacher);
        }
        if (Auth::user()->staff->first()) {
            $centerTeacher = CenterTeacher::where('center_id', Session()->get('center_id'))->select('teacher_id')->pluck('teacher_id');
            return $query->whereIn('id', $centerTeacher);
        }
        return $query;
    }

    //Getter Attributes
    public function getImageAttribute($value)
    {
        return url(Storage::url($value));
    }

    public function getQualificationCertificateAttribute($value)
    {
        if (!empty($value)) {
            return url(Storage::url($value));
        }
    }
}
