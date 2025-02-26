<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\PaymentTransaction
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $parent_id
 * @property int $mode 0 - cash 1 - cheque 2 - online
 * @property string|null $cheque_no
 * @property int $type_of_fee 0 - compulsory_full , 1 - installments , 2 -optional
 * @property int|null $payment_gateway 1 - razorpay 2 - stripe
 * @property string|null $order_id order_id / payment_intent_id
 * @property string|null $payment_id
 * @property string|null $payment_signature
 * @property int $payment_status 0 - failed 1 - succeed 2 - pending
 * @property float $total_amount
 * @property int $amount_paid
 * @property int $fees_left
 * @property string|null $date
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereFeesLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereTypeOfFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction withoutTrashed()
 * @mixin \Eloquent
 */
class PaymentTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function student(){
        return $this->belongsTo(Students::class ,'student_id')->withTrashed();
    }
    public function class() {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }
    public function session_year() {
        return $this->belongsTo(SessionYear::class);
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
