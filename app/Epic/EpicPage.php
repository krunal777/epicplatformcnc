<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicPage extends Eloquent
{
    protected $table = 'pages';
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EpicOrganisationStoreScope);
    }
}
