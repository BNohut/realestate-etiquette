<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use App\Models\State;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Province extends Model
{
    use HasFactory, AsSource, Filterable, SoftDeletes;


    protected $table = 'provinces';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'plate_number',
        'phone_code',
        'tax_offices'
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'plate_number' => Like::class,
        'phone_code' => Like::class
    ];

    protected $allowedSorts = [
        'name',
        'plate_number',
        'phone_code'
    ];

    public function state()
    {
        return $this->hasMany(State::class);
    }
}
