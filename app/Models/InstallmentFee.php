<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\InstallmentFee
 *
 * @property int $id
 * @property string $name
 * @property string $due_date
 * @property int $due_charges in percentage (%)
 * @property int $session_year_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee withoutTrashed()
 * @mixin \Eloquent
 */
class InstallmentFee extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ["created_at", "updated_at"];

    public function session_year(){
        return $this->belongsTo(SessionYear::class,'session_year_id');
    }

    //Getter Attributes
    public function getDueDateAttribute($value) {
        $data = getSettings('date_formate');
        return date($data['date_formate'] ?? 'd-m-Y' ,strtotime($value));
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')){
            return $query->where('center_id',Auth::user()->center->id);
        }
        return $query;

    }
}
