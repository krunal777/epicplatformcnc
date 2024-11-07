<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicBrands extends Eloquent
{
    protected $table = 'brands';
    protected $guarded = [];
}