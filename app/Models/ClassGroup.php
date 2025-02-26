<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClassGroup
 *
 * @property int $id
 * @property int $group_id
 * @property int $class_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\Group $group
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'group_id',
        'center_id',
        'created_at',
        'updated_at'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the group that owns the ClassGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the class that owns the ClassGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(ClassSchool::class,'class_id');
    }
}
