<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'origin' => 'nullable|string|max:1000',
            'destination' => 'nullable|string|max:1000',
            'estimated_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
