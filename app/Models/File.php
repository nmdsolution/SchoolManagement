<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $modal_type
 * @property int $modal_id
 * @property string|null $file_name
 * @property string|null $file_thumbnail
 * @property string $type 1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
 * @property string $file_url
 * @property int $downloadable 0 -> No, 1 -> Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $file_extension
 * @property-read mixed $type_detail
 * @property-read Model|\Eloquent $modal
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDownloadable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = array('file_extension', 'type_detail');

    protected $fillable = ['modal_id','modal_type','file_name','type','file_url','downloadable'];

    protected static function boot() {
        parent::boot();
        static::deleting(function ($file) { // before delete() method call this
            if (Storage::disk('public')->exists($file->file_url)) {
                Storage::disk('public')->delete($file->file_url);
            }
        });
    }

    public function modal() {
        return $this->morphTo();
    }

    //Getter Attributes
    public function getFileUrlAttribute($value) {
        if ($this->type == 1 || $this->type == 3) {
            // IF type is File Upload or Video Upload then add Full URL.
            return url(Storage::url($value));
        } else {
            // ELSE return the value as it is.
            return $value;
        }
    }

    //Getter Attributes
    public function getFileThumbnailAttribute($value) {
        if (!empty($value)) {
            return url(Storage::url($value));
        } else {
            return "";
        }
    }

    public function getFileExtensionAttribute() {
        if (!empty($this->file_url)) {
            return pathinfo(url(Storage::url($this->file_url)), PATHINFO_EXTENSION);
        } else {
            return "";
        }
    }

    public function getTypeDetailAttribute() {
        //1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
        if ($this->type == 1) {
            return "File Upload";
        } elseif ($this->type == 2) {
            return "Youtube Link";
        } elseif ($this->type == 3) {
            return "Video Upload";
        } elseif ($this->type == 4) {
            return "Other Link";
        }
    }
}
