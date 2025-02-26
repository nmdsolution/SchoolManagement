<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\PaidInstallmentFee
 *
 * @property int $id
 * @property int $class_id
 * @property int $student_id
 * @property int|null $parent_id
 * @property int $installment_fee_id
 * @property int $session_year_id
 * @property float $amount
 * @property float|null $due_charges
 * @property string $date
 * @property int $payment_transaction_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\InstallmentFee $installment_fee
 * @property-read \App\Models\Parents|null $parent
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereInstallmentFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaidInstallmentFee extends Model
{
    use HasFactory;
    protected $hidden = ["created_at", "updated_at"];

    public function class(){
        return $this->belongsTo(ClassSchool::class,'class_id');
    }

    public function student(){
        return $this->belongsTo(Students::class,'student_id');
    }

    public function parent(){
        return $this->belongsTo(Parents::class,'parent_id');
    }

    public function installment_fee(){
        return $this->belongsTo(InstallmentFee::class,'installment_fee_id');
    }

    public function session_year(){
        return $this->belongsTo(SessionYear::class,'session_year_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')){
            return $query->where('center_id',Auth::user()->center->id);
        }
        return $query;
    }

    public function scopeCurrentSessionYear($query){
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }

}
