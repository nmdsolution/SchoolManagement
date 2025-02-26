<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use \Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\IncomeCategory
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $status
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Income> $incomes
 * @property-read int|null $incomes_count
 * @property-read \App\Models\Mediums $medium
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory owner()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IncomeCategory extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'title', 'description', 'status', 'slug', 'center_id', 'medium_id'
    ];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'category_id', 'id');
    }

    public function scopeOwner($query) {
        if (get_center_id()) {
            return $query->where('center_id', get_center_id());
        }
        return $query;
    }

    public function medium() {
        return $this->belongsTo(Mediums::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'slug' => $this->slug,
            'center_id'=> $this->center_id,
            'medium' => $this->medium_id != 0 ? $this->medium->name : "All",
            'date' => $this->created_at->toString()
        ];
    }
}
