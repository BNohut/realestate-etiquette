<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use App\Models\Province;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class State extends Model
{
    use HasFactory, AsSource, Filterable, SoftDeletes;

    protected $table = 'states';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'province_id'
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
    ];

    protected $allowedSorts = [
        'name',
    ];

    public function province()
    {
        return $this->hasOne(Province::class, "id", "province_id");
    }
}
