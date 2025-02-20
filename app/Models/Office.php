<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Office extends Model
{
    use HasFactory, AsSource, SoftDeletes, Attachable, Filterable;

    protected $table = 'offices';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'province_id',
        'state_id',
        'neighborhood',
        'street',
        'building_no',
        'apartment_no',
        'social_media_accounts',
        'website',
        'logo'
    ];

    public function getManagerNameAttribute()
    {
        if ($this->user_id == null) return null;

        $user = User::find($this->user_id);

        return $user->name . ' ' . $user->last_name;
    }
}
