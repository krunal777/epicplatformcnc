<?php

namespace App\Epic;

use MongoDB\Laravel\Eloquent\Model as Eloquent;
use App\Epic\EpicOrganisationStoreScope;

class EpicThemeTemplates extends Eloquent
{
    protected $table = 'theme_templates';
    protected $guarded = [];
}
