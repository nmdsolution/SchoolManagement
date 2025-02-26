<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Guardian
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $student_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $guardianRelationChild
 * @property-read int|null $guardian_relation_child_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian query()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereUserId($value)
 * @mixin \Eloquent
 */
class Guardian extends Model {
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the user that owns the Guardian
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function guardianRelationChild() {
        return $this->hasMany(Students::class);
    }


    public function children() {
        return $this->union($this->guardianRelationChild());
    }
}
