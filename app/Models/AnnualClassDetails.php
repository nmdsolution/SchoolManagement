<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnualClassDetails
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $student_id
 * @property float $avg
 * @property int $rank
 * @property string $term_avgs
 * @property string $term_ranks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnnualReport|null $annual_report
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\AnnualReport|null $exam_report
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereTermAvgs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereTermRanks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualClassDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'annual_report_id',
        'class_section_id',
        'student_id', 
        "avg",
        'rank',
        'term_avgs',
        'term_ranks'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id')->withTrashed();
    }

    public function exam_report()
    {
        return $this->belongsTo(AnnualReport::class);
    }

    public function getTermAvgsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getTermRanksAttribute($value)
    {
        return json_decode($value, true);
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function annual_report() {
        return $this->belongsTo(AnnualReport::class, 'annual_report_id');
    }
}
