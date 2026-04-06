<?php

namespace App\Queries;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductIndexQuery
{
    public function execute(array $filters): LengthAwarePaginator
    {
        return Product::query()
            ->with(['baseCurrency', 'prices.currency'])
            ->filter($filters)
            ->orderBy(
                $filters['sort_by'] ?? 'id',
                $filters['sort_direction'] ?? 'desc',
            )
            ->paginate($filters['per_page'] ?? 15);
    }
}
