<?php

namespace App\Models;

use App\Presenters\BladePresenter;
use App\Traits\UserPermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereMaxMin;
use Illuminate\Database\Eloquent\Builder;

class Portfolio extends Model
{
    use HasFactory, Attachable, Searchable, Filterable, SoftDeletes, UserPermissionTrait;
    protected $table = 'portfolios';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'contact_id',
        'title',
        'portfolio_no',
        'link',
        'square_net',
        'square_total',
        'portfolio_type',
        'portfolio_group',
        'portfolio_variation',
        'portfolio_resource',
        'ada_no',
        'parsel_no',
        'street',
        'building_no',
        'apartment_no',
        'province_id',
        'state_id',
        'neighborhood',
        'latitude',
        'longitude',
        'list_price',
        'minimum_price',
        'sale_price',
        'description',
        'contract_date',
        'deed_status',
        'images',
        'slug'
    ];

    protected $allowedFilters = [
        'user_id' => Where::class,
        'title' => Like::class,
        'portfolio_no' => Like::class,
        'link' => Like::class,
        'square_net' => WhereMaxMin::class,
        'square_total' => WhereMaxMin::class,
        'portfolio_type' => Like::class,
        'portfolio_group' => Like::class,
        'portfolio_variation' => Like::class,
        'portfolio_resource' => Like::class,
        'ada_no' => Like::class,
        'parsel_no' => Like::class,
        'street' => Like::class,
        'building_no' => Like::class,
        'apartment_no' => Like::class,
        'province_id' => Like::class,
        'state_id' => Like::class,
        'neighborhood' => Like::class,
        'latitude' => Like::class,
        'longitude' => Like::class,
        'list_price' => Like::class,
        'minimum_price' => Like::class,
        'sale_price' => Like::class,
        'description' => Like::class,
        'contract_date' => Like::class,
        'deed_status' => Like::class,
    ];

    protected $appends = [
        'contact_name',
        'consultant_name',
        'magic_link'
    ];

    public function toSearchableArray()
    {
        $array = [];
        $array['id'] = $this->id;
        $array['title'] = $this->title;
        $array['portfolio_no'] = $this->portfolio_no;
        $array['province'] = $this->provinceS->name;
        $array['state'] = $this->stateS->name;
        $array['neighborhood'] = $this->neighborhood;
        $array['contact_name'] = $this->contact_name;
        $array['consultant_name'] = $this->consultant_name;
        $array['facet'] = "Portföyler";

        return $array;
    }

    public function getMagicLinkAttribute()
    {
        if ($this->user_id == null) {
            return null;
        }
        $user = User::where('id', $this->user_id)->first();
        if ($user->inRole('bireysel-danisman')) {
            return null;
        }
        return route('magic', $this->slug);
    }

    public function getConsultantNameAttribute()
    {
        if ($this->user_id == null) {
            return null;
        }
        $consultant = User::where('id', $this->user_id)->first();
        return $consultant->name . " " . $consultant->last_name;
    }

    public function getContactNameAttribute()
    {
        if ($this->contact_id == null) {
            return null;
        }
        $contact = Contact::where('id', $this->contact_id)->first();
        return $contact->name;
    }

    public function userS(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function provinceS(): HasOne
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }
    public function stateS(): HasOne
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }
    public function contactS(): HasOne
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }

    public function presenter(): BladePresenter
    {
        return new BladePresenter($this);
    }

    public function scopeOfficeOrConsultant(Builder $query)
    {
        $user = auth()->user();
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            return $query->get();
        }
        if (authUserInRole(['ofis-yoneticisi', 'ofis-asistani'])) {
            return $query->join('users', 'portfolios.user_id', '=', 'users.id')
                ->where('users.office_id', $user->office_id)->select('portfolios.*')->get();
        }
        if (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
            return $query->where('user_id', $user->id)->select('portfolios.*')->get();
        }
    }

    // Generating slug for magic link
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = slugGeneratorForMagicLink($model->title);
        });
    }
}
