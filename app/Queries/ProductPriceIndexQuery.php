<?php

namespace App\Queries;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductPriceIndexQuery
{
    public function execute(Product $product, array $filters): LengthAwarePaginator
    {
        return $product->prices()
            ->with('currency')
            ->when(
                $filters['currency_id'] ?? null,
                fn ($query, $currencyId) => $query->where('currency_id', $currencyId),
            )
            ->orderBy(
                $filters['sort_by'] ?? 'id',
                $filters['sort_direction'] ?? 'desc',
            )
            ->paginate($filters['per_page'] ?? 15);
    }
}
