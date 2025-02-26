<?php

namespace App\Models;

use App\Models\Center;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Expense
 *
 * @property int $id
 * @property int|null $center_id
 * @property int|null $session_year_id
 * @property string $item_name
 * @property int $qty
 * @property float $amount
 * @property string|null $purchase_by
 * @property string|null $purchase_from
 * @property string $date
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Center|null $centers
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePurchaseBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePurchaseFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withoutTrashed()
 * @mixin \Eloquent
 */
class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable=[
        'center_id',
        'item_name',
        'qty',
        'amount',
        'purchase_by',
        'purchase_from',
        'date',
        'total_amount',
    ];

    public function centers()
    {
        return $this->belongsTo(Center::class);
    }
}
