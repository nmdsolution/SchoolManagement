<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Settings
 *
 * @method static where(string $string, string $string1)
 * @property int $id
 * @property string $type
 * @property string $message
 * @property int|null $center_id
 * @property string $data_type text / file
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|Settings currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereType($value)
 * @mixin \Eloquent
 */
class Settings extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "type",
        "message",
        "center_id",
        "data_type",
        "medium_id"
    ];

    public function getMessageAttribute($value)
    {
        if ($this->data_type === "file") {
            return url(Storage::url($value));
        }

        return $value;
    }

    public function scopeCurrentMedium($query){
        $query->where('medium_id', getCurrentMedium()->id);
    }
}
