<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnualClassSubjectReport
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property float $min
 * @property float $max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualClassSubjectReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'annual_report_id',
        'class_section_id',
        'subject_id', 
        "min",
        'max',
        'term_mins',
        'term_maxs'
    ];
}
