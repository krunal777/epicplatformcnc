<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicCarts extends Eloquent
{
    protected $table = 'carts';
    protected $guarded = [];
}
