<?php

namespace App\Http\Requests;

use App\Models\States\Offer\OfferStates;
use App\CoreLogic\Enum\Offer\OfferTypeEnum;
use App\CoreLogic\Enum\Offer\PromoCodeTypeEnum;
use App\CoreLogic\Enum\Offer\VoucherTypeEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\ModelStates\Validation\ValidStateRule;

class StoreOfferRequest extends FormRequest
{
    use FormRequestHelperTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->tokenCan('inventory.promocode.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $essentialFieldRules = [
            "title" => "required|string|max:255",
            "amount" => "required|regex:/^\d*(\.\d{1,2})?$/",
            "status" => ["nullable", new ValidStateRule(OfferStates::class)],
            "offer_type" => ["required", Rule::enum(OfferTypeEnum::class)],
        ];

        $fieldTypeRules = [];
        if ($this->has('offer_type')) {
            if (OfferTypeEnum::tryFrom($this->get('offer_type')) === OfferTypeEnum::PROMO_CODE) {
                $fieldTypeRules = [
                    "code" => "required|string|max:255|unique:offers,code",
                    "code_type" => ['required', Rule::enum(PromoCodeTypeEnum::class)],
                ];
            } elseif (OfferTypeEnum::tryFrom($this->get('offer_type')) === OfferTypeEnum::VOUCHER) {
                $fieldTypeRules = [
                    "code_type" => ['required', Rule::enum(VoucherTypeEnum::class)],
                    "quantity" => [
                        'numeric', 'min:0', 'max:100',
                        Rule::when(
                            static fn($input
                            ) => (VoucherTypeEnum::tryFrom($input->code_type) === VoucherTypeEnum::AUTOMATIC),
                            'required'
                        )
                    ],
                    "code" => [
                        'string', 'max:255', 'unique:offers,code',
                        Rule::when(
                            static fn($input
                            ) => (VoucherTypeEnum::tryFrom($input->code_type) === VoucherTypeEnum::MANUAL),
                            'required'
                        )
                    ],
                ];
            }
        }

        $resourceRules = [
            "products" => "required|array",
        ];

        $validationRules = [
            "start_at" => "nullable|date_format:Y-m-d H:i:s|after_or_equal:" . Carbon::now()->format('Y-m-d'),
            "expired_at" => "nullable|date_format:Y-m-d H:i:s|after:start_at",
            "min_order_price" => "nullable|integer",
            "max_order_price" => "nullable|integer|gt:min_order_price",
            "apply_once_per_type" => "nullable|in:" . implode(",", config('app.promocode.apply_once_per_type')),
            "max_redemption_time" => "nullable|integer",
            "include_tax_fee" => "nullable|boolean"
        ];
        return array_merge($essentialFieldRules, $resourceRules, $validationRules, $fieldTypeRules);
    }
}
