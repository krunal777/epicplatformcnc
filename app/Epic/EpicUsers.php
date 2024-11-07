<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicUsers extends Eloquent
{
    protected $table = 'customers';
    protected $guarded = [];
}
