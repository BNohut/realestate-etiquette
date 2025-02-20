<?php

namespace App\Traits;

trait UserPermissionTrait
{
    public static function boot()
    {
        $user = auth()->user();

        parent::boot();

        static::updating(function ($model) use ($user) {

            if (!$user->canDeleteOrUpdateRecord($model)) {
                throw new \Exception("Bu kaydı güncelleme yetkiniz yok!");
            }
        });

        static::deleting(function ($model) use ($user) {

            if (!$user->canDeleteOrUpdateRecord($model)) {
                throw new \Exception("Bu kaydı silme yetkiniz yok!");
            }
            return true;
        });
    }
}
