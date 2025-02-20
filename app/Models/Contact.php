<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Laravel\Scout\Searchable;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;

class Contact extends Model
{
    use HasFactory, Filterable, Searchable, AsSource, Attachable, SoftDeletes;

    protected $table = 'contacts';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'gender',
        'province_id',
        'state_id',
        'neighborhood',
        'address',
        'profession',
        'details',
        'avatar'
    ];

    public function toSearchableArray()
    {
        $array = [];
        $array['id'] = $this->id;
        $array['name'] = $this->name;
        $array['email'] = $this->email;
        $array['facet'] = "Kişiler";
        return $array;
    }

    protected $allowedFilters = [
        'id',
        'name'
    ];

    protected $allowedSorts = [
        'name',
    ];

    public function province(): HasOne
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function scopeConsultant($query)
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            return $query->all();
        } elseif (authUserInRole(['ofis-yoneticisi', 'ofis-asistani'])) {
            return $query->join('users', 'users.id', 'contacts.user_id')
                ->where('users.office_id', auth()->user()->office_id)
                ->select('contacts.*')->get();
        }
        return $query->where('user_id', auth()->user()->id)->get();
    }
}
