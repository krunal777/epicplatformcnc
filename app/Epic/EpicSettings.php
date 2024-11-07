<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicSettings extends Eloquent
{
    protected $table = 'settings';
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EpicOrganisationStoreScope);
    }
}
