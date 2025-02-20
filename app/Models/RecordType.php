<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

class RecordType extends Model
{
    use HasFactory, Filterable, AsSource, SoftDeletes;

    protected $table = 'record_types';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
    ];

    protected $allowedFilters = [
        'name' => Like::class,
    ];
}
