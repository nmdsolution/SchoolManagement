<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClassReportCard
 *
 * @property int $id
 * @property int $class_id
 * @property int $report_card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read mixed $report_name
 * @property-read \App\Models\ReportCard $reportCard
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard whereReportCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_id',
        'class_id',
    ];

    public function reportCard() {
        return $this->belongsTo(ReportCard::class);
    }

    public function class() {
        return $this->belongsTo(ClassSchool::class);
    }

    public function getReportNameAttribute() {
        return $this->reportCard->name ?? '-';
    }
}
