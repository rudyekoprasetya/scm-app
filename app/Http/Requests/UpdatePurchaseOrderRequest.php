<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update-purchase-orders');
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'order_date' => 'sometimes|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ];
    }
}
