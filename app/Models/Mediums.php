<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * App\Models\Mediums
 *
 * @property int $id
 * @property string $name
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withoutTrashed()
 * @mixin \Eloquent
 */
class Mediums extends Model
{
    use SoftDeletes;
    use HasFactory;
    public $timestamps = false;
}
