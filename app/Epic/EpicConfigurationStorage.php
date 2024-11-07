<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicConfigurationStorage extends Eloquent
{
    protected $table = 'configuration_storage';
    protected $guarded = [];
}
