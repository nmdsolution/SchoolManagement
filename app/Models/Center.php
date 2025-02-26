<?php

namespace App\Models;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Rawilk\Settings\Models\HasSettings;

/**
 * App\Models\Center
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $support_contact
 * @property string $support_email
 * @property \Illuminate\Contracts\Routing\UrlGenerator|\Illuminate\Contracts\Foundation\Application|string $logo
 * @property string $tagline
 * @property string $address
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Expense> $expenses
 * @property-read int|null $expenses_count
 * @property-read \App\Models\TimetableTemplate|null $timetableTemplate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereSupportContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereSupportEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center withoutTrashed()
 * @mixin \Eloquent
 */
class Center extends Model {
    use HasFactory;
    use SoftDeletes;
    use HasSettings;

    public const TYPE_SECONDARY = 0;
    public const TYPE_PRIMARY = 1;

    protected $fillable = [
        'name',
        'support_email',
        'support_contact',
        'logo',
        'tagline',
        'address',
        'user_id',
        'type'
    ];

    protected static function boot() {
        parent::boot();
        static::deleting(static function ($center) { // before delete() method call this
            if ($center->isForceDeleting()) {
                if (Storage::disk('public')->exists($center->getRawOriginal('logo'))) {
                    Storage::disk('public')->delete($center->getRawOriginal('logo'));
                }
                $center->user()->forceDelete();
                $center->forceDelete();
            }
        });
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class)->withTrashed();
    }


    public function getLogoAttribute($value): string|UrlGenerator|Application {
        return url(Storage::url($value));
    }

    // public function getTypeAttribute($value): string
    // {
    //     return match ($value) {
    //         self::TYPE_PRIMARY => 'primary',
    //         default => 'secondary'
    //     };
    // }

    public function expenses() {
        return $this->hasMany(Expense::class);
    }

    public function timetableTemplate() {
        return $this->belongsTo(TimetableTemplate::class);
    }

    public function centerReport(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CenterReport::class);
    }
}
