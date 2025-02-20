<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Follower extends Model
{
    use HasFactory, AsSource, Filterable;


    protected $table = 'followers';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'from',
        'to',
        'approved',
    ];

    protected $allowedFilters = [
        'id' => Where::class,
        'from' => Like::class,
        'to' => Like::class,
        'approved' => Like::class
    ];

    protected $allowedSorts = [
        'id',
        'from',
        'to',
        'approved'
    ];

    protected $appends = ['from_name', 'to_name'];

    public function getFromNameAttribute()
    {
        $user = User::find($this->from);
        return $user->name . ' ' . $user->last_name;
    }

    public function getToNameAttribute()
    {
        $user = User::find($this->to);
        return $user->name . ' ' . $user->last_name;
    }
}
