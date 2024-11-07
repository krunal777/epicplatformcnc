<?php

namespace App\Search;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomSearchStrategy
{
    protected array $searchableFields = ['name'];
    protected ?Request $request = null;
    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }
    public function setSearchableFields(array $fields): void
    {
        $this->searchableFields = $fields;
    }

    public function applySearch(Builder $builder, string $phrase): void
    {
        if (empty($phrase)) {
            return;
        }

        // Apply search to all searchable fields
        $builder->where(function ($query) use ($phrase) {
            $query->where('name', 'LIKE', "%{$phrase}%");
        });
        if ($this->request && $this->request->has('with') && in_array('children', explode(',', (string) $this->request->get('with')), true)) {
            // No additional filter applied in this case, just return the query
            return;
        }

        // Apply condition if 'parent_id' is null
        $builder->whereNull('parent_id');
    }

    public function applyScoreSort(Builder $builder): void
    {
        // Ensure that your model has a 'score' field or adjust as necessary
        $builder->orderBy('score', 'desc');
    }
}
