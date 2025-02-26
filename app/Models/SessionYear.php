<?php

namespace App\Models;

use App\Http\Controllers\StudentSessionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SessionYear
 *
 * @property int $id
 * @property string $name
 * @property int $default
 * @property string $start_date
 * @property string $end_date
 * @property int $include_fee_installments 0 - no 1 - yes
 * @property string $fee_due_date
 * @property int $fee_due_charges in percentage (%)
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InstallmentFee> $fee_installments
 * @property-read int|null $fee_installments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentSessions> $studentSessions
 * @property-read int|null $student_sessions_count
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereFeeDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereFeeDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereIncludeFeeInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withoutTrashed()
 * @mixin \Eloquent
 */
class SessionYear extends Model
{
    use SoftDeletes;
    use HasFactory;

    public function fee_installments() {
        return $this->hasMany(InstallmentFee::class, 'session_year_id')->withTrashed();
    }
    public function scopeOwner($query)
    {
        return $query->where('center_id',get_center_id());
    }

    public function studentSessions() {
        return $this->hasMany(StudentSessions::class, 'session_year_id');
    }

    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year'])->first();
    }


}
