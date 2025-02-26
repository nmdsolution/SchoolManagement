<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Parents
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $gender
 * @property string|null $email
 * @property string|null $mobile
 * @property string|null $occupation
 * @property string|null $image
 * @property string|null $dob
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseStudent> $coursesStudent
 * @property-read int|null $courses_student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $fatherRelationChild
 * @property-read int|null $father_relation_child_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $guardianRelationChild
 * @property-read int|null $guardian_relation_child_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $motherRelationChild
 * @property-read int|null $mother_relation_child_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Parents newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents query()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents withoutTrashed()
 * @mixin \Eloquent
 */
class Parents extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function fatherRelationChild() {
        return $this->hasMany(Students::class, 'father_id');
    }

    public function motherRelationChild() {
        return $this->hasMany(Students::class, 'mother_id');
    }

    public function guardianRelationChild() {
        return $this->hasMany(Students::class, 'guardian_id');
    }

    public function guardian() {
        $guardian = Guardian::where('user_id', Auth::user()->id)->get()->pluck('student_id');
        return Students::whereIn('id', $guardian);
    }

    public function children() {
        return $this->fatherRelationChild()->union($this->motherRelationChild())->union($this->guardianRelationChild())->union($this->guardian());
    }

    //Getter Attributes
    public function getImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Center')) {
            return $query->whereHas('fatherRelationChild', function ($q) {
                $q->where('center_id', Auth::user()->center->id);
            })
                ->orWhereHas('motherRelationChild', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                })
                ->orWhereHas('guardianRelationChild', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                });
        } else if (Auth::user()->staff->first()) {
            return $query->whereHas('fatherRelationChild', function ($q) {
                $q->where('center_id', Session()->get('center_id'));
            })
                ->orWhereHas('motherRelationChild', function ($q) {
                    $q->where('center_id', Session()->get('center_id'));
                })
                ->orWhereHas('guardianRelationChild', function ($q) {
                    $q->where('center_id', Session()->get('center_id'));
                });
        }  else if (Auth::user()->hasRole('Teacher')) {
            return $query->whereHas('fatherRelationChild', function ($q) {
                $q->where('center_id', Session()->get('center_id'));
            })
                ->orWhereHas('motherRelationChild', function ($q) {
                    $q->where('center_id', Session()->get('center_id'));
                })
                ->orWhereHas('guardianRelationChild', function ($q) {
                    $q->where('center_id', Session()->get('center_id'));
                });
        }
        return $query;
    }

    public function coursesStudent() {
        return $this->belongsToMany(CourseStudent::class, 'course_students', 'course_id', 'student_id');
    }
}
