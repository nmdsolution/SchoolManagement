<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\FeesClass
 *
 * @property int $id
 * @property int $class_id
 * @property int $fees_type_id
 * @property int $choiceable 0 - no 1 - yes
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paid
 * @property-read int|null $fees_paid_count
 * @property-read \App\Models\FeesType $fees_type
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereChoiceable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereFeesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass withoutTrashed()
 * @mixin \Eloquent
 */
class FeesClass extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    // This class is all about assigning a particular fees to a particular class with the amount associated with the
    // class.

    public function fees_type(){
        return $this->belongsTo(FeesType::class ,'fees_type_id');
    }

    public function fees_paid() {
        return $this->hasMany(FeesPaid::class, 'fees_class_id');
    }

    public function class() {
        return $this->belongsTo(ClassSchool::class, 'class_id')->with('medium');
    }
    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')){
            return $query->where('center_id',Auth::user()->center->id);
        }
        return $query;
    }
}
