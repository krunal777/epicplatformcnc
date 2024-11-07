<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicOrder extends Eloquent
{
    protected $table = 'orders';
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EpicOrganisationStoreScope);
    }
}
