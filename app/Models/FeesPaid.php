<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\FeesPaid
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $mode 0 - cash 1 - cheque 2 - online
 * @property string|null $payment_transaction_id
 * @property string|null $cheque_no
 * @property float $total_amount
 * @property int $is_fully_paid 0 - no 1 - yes
 * @property int $amount_paid
 * @property string $date
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\PaymentTransaction|null $payment_transaction
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereIsFullyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withoutTrashed()
 * @mixin \Eloquent
 */
class FeesPaid extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function session_year()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id')->withTrashed();
    }

    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id')->withTrashed();
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')){
            return $query->where('center_id',Auth::user()->center->id);
        }
        return $query;

    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
