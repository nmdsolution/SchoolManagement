<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReportCard
 *
 * @property int $id
 * @property string $name
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'card_id'];
}
