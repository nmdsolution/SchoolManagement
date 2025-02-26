<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

/**
 * App\Models\Income
 *
 * @property int $id
 * @property string $title
 * @property string|null $invoice_id
 * @property string $amount
 * @property string $date
 * @property string|null $reference
 * @property int|null $payment_method
 * @property string|null $note
 * @property string|null $attach
 * @property int $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $category_id
 * @property int $session_year_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\IncomeCategory $category
 * @property-read \App\Models\Center $center
 * @property-read \App\Models\Mediums $medium
 * @method static \Illuminate\Database\Eloquent\Builder|Income newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Income newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Income owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Income query()
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereAttach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Income extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'category_id', 'name', 'invoice_id', 'amount', 'date', 'purchased_by', 'purchased_from',
        'payment_method', 'note', 'attach', 'medium_id', 'session_year_id', 'center_id'
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'category_id');
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('center')) {
            return $query->where('center_id', get_center_id());
        } else {
            return $query;
        }
    }

    public function recordedBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function medium() {
        return $this->belongsTo(Mediums::class, 'medium_id');
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'purchased_by' => $this->purchased_by,
            'purchased_from' => $this->purchased_from,
            'category_id' => $this->category_id,
            'medium' => $this->medium->name
        ];
    }
}
