<?php

namespace App\Http\Requests;

use App\CoreLogic\Enum\Deductible\DeductibleCategoryEnum;
use App\CoreLogic\Enum\Deductible\DeductibleTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeductibleRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->tokenCan('inventory.deductible');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required', 'string',
                Rule::unique('deductibles', 'name')
                    ->where('tenant_id', tenant()->getKey()),
                'max:255'],
            'category' => ['required', Rule::enum(DeductibleCategoryEnum::class)],
            'type' => ['required', Rule::enum(DeductibleTypeEnum::class)],
            'value' => [
                'required', 'numeric', 'min:0',
                Rule::when(
                    static fn($input) => (DeductibleTypeEnum::tryFrom($input->type) === DeductibleTypeEnum::PERCENTAGE),
                    'max:100'
                )
            ],
            'is_price_inclusive' => ['boolean'],
            'is_compounded' => ['boolean'],
        ];
    }
}
