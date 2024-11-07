<?php

namespace App\Epic;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EpicOrganisationStoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $organisationId = (int) request()->header('X-Organisation-Id');
        $storeId = (int) request()->header('X-Store-Id');

        // Apply the constraints
        if ($organisationId && $storeId) {
            $builder->where('organisation', $organisationId)
                    ->where('store', $storeId);
        }
    }
}
