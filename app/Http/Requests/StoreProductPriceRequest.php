<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::unique('product_prices', 'currency_id')
                    ->where('product_id', $this->route('product')->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Product $product */
                $product = $this->route('product');
                $currencyId = (int) $this->input('currency_id');
                $price = round((float) $this->input('price'), 2);
                $basePrice = round((float) $product->price, 2);

                if ($currencyId === (int) $product->currency_id && $price === $basePrice) {
                    $validator->errors()->add(
                        'price',
                        'The base product price already exists in the product record and must not be duplicated in additional prices.',
                    );
                }

                if ($currencyId === (int) $product->currency_id) {
                    $validator->errors()->add(
                        'currency_id',
                        'An additional product price must use a currency different from the base product currency.',
                    );
                }
            },
        ];
    }
}
