<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\FeesChoiceable
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $fees_type_id
 * @property int $is_due_charges 0 - no 1 - yes
 * @property float $total_amount
 * @property int $session_year_id
 * @property string|null $date
 * @property int|null $payment_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\FeesType|null $fees_type
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereFeesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereIsDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeesChoiceable extends Model {
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function fees_type() {
        return $this->belongsTo(FeesType::class, 'fees_type_id');
    }

    public static function scopeOwner($query) {
        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->staff->first()) {
            if (Session()->get('center_id') != -1) {
                return $query->where('center_id', Session()->get('center_id'));
            }

            return $query->where('center_id', NULL);
        }
        return $query;
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
