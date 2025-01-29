<?php

namespace App\Http\Requests;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->tokenCan('inventory.asset.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique('assets')->where('tenant_id', tenant()->getKey())],
            'quantity' => 'required|integer',
            'capacity_per_quantity' => 'required|integer',
            'shared_between_products' => 'required|boolean',
            'shared_between_bookings' => 'required|boolean',
            'maintenance_booking_gap_minutes' => 'nullable|integer',
            'category_ids' => 'required|array',
            'category_ids.*' => 'required|exists:categories,id,tenant_id,' . tenant()->getKey(),
        ];
    }
}
