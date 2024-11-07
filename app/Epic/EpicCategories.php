<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicCategories extends Eloquent
{
    protected $table = 'categories';
    protected $guarded = [];
}