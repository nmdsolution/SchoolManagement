<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\ExamTerm
 *
 * @property int $id
 * @property string $name
 * @property int $session_year_id
 * @property int $center_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamSequence> $sequence
 * @property-read int|null $sequence_count
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_year_id',
        'center_id',
        'medium_id',
        'start_date',
        'end_date'
    ];
    public function examSequences()
    {
        return $this->hasMany(ExamSequence::class, 'exam_term_id');
    }

    public function session_year()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function sequence()
    {
        return $this->hasMany(ExamSequence::class);
    }

    public function scopeOwner($query, $center_id = null)
    {
        if (Auth::user()->hasRole('Center')) {
            $query = $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher') && session()->get('center_id')) {
            $query = $query->where('center_id', session()->get('center_id'));
        }
        return $query;
    }

    public function scopeCurrentSessionYear($query)
    {
        $session_year = getSettings('session_year');
        return $query->where('session_year_id', $session_year['session_year']);
    }

    public function scopeCurrentMedium($query){
        $query->where('medium_id', getCurrentMedium()->id);
    }
}
