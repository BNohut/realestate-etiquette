<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Platform\Models\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;

class User extends Authenticatable
{
    use SoftDeletes, Attachable, Filterable, Searchable;
    protected $table = 'users';
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'last_name',
        'user_name',
        'email',
        'phone',
        'visibility',
        'url',
        'province_id',
        'state_id',
        'neighborhood',
        'json',
        'avatar',
        'office_id',
        'permissions',
        'about_me',
        "office_approved_at",
        "push_token",
        "email_verified_at",
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Where::class,
        'last_name' => Where::class,
        'user_name' => Where::class,
        'email' => Where::class,
        'phone' => Where::class,
        'province_id' => Where::class,
        'state_id' => Where::class,
        'neighborhood' => Where::class,
        'type' => Where::class,
        'avatar' => Where::class,
        'office_id' => Where::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    public function toSearchableArray()
    {
        if ($this->inRole('ofis-danismani') || $this->inRole('bireysel-danisman')) {
            $array = [];
            $array['id'] = $this->id;
            $array['full_name'] = $this->getFullNameAttribute();
            $array['email'] = $this->email;
            $array['province'] = $this->provinceS?->name;
            $array['office_name'] = $this->officeS?->name;
            $array['facet'] = "Danışmanlar";

            return $array;
        }
        return [];
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' ' . $this->last_name ?? '';
    }

    public function getFullAddressAttribute(): string
    {
        $provinceName = findProvinceName($this->province_id);
        $stateName = findStateName($this->state_id);
        return $provinceName . ' / ' . $stateName . ' / ' . $this->neighborhood;
    }

    public function getOfficeNameAttribute(): string
    {
        if ($this->office_id == null) {
            return '';
        }
        $office = Office::find($this->office_id);
        return $office->name;
    }

    public function provinceS(): HasOne
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function stateS(): HasOne
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }

    public function officeS(): HasOne
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function getFullAttribute(): string
    {
        return $this->attributes['name'] . ' ' . $this->attributes['last_name'];
    }

    public function canDeleteOrUpdateRecord($record)
    {
        $isAdmin = $this->inRole('super-yonetici');

        return $record->user_id == $this->id || $isAdmin;
    }

    public function scopeManager(Builder $query)
    {
        $return =
            $query->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->leftJoin('offices', 'users.id', '=', 'offices.user_id')
            ->where('roles.slug', 'ofis-yoneticisi')
            ->whereNull('offices.user_id') // offices tablosunda kaydı olmayanları seç
            ->select('users.*')
            ->get();
        return $return;
    }

    public function scopeConsultant(Builder $query)
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            return $query->join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])->select('users.*')->get();
        }
        if (authUserInRole(['ofis-yoneticisi', 'ofis-asistani', 'ofis-danismani'])) {
            $user = auth()->user();
            return $query->join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('office_id', $user->office_id)->select('users.*')->get();
        }
    }

    public function scopeConsultantForViewing(Builder $query)
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            return $query->join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])->select('users.*')->get();
        }
        if (authUserInRole(['ofis-yoneticisi', 'ofis-asistani', 'ofis-danismani'])) {
            $user = auth()->user();
            return $query->join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('office_id', $user->office_id)->select('users.*')->get();
        }
    }
}
