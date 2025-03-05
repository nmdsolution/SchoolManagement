<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'page_layout',
        'height',
        'width',
        'user_image_shape',
        'image_size',
        'certificate_title',
        'certificate_heading',
        'certificate_text',
        'background_image',
        'custom_css',
        'background_image_path',
        'is_default',
        'center_id'
    ];

    // Scope for current center
    public function scopeCurrentCenter($query)
    {
        return $query->where('center_id', get_center_id());
    }
}
