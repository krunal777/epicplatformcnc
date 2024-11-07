<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicProduct extends Eloquent
{
    protected $table = 'products';
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EpicOrganisationStoreScope);
    }
}