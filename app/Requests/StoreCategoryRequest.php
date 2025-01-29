<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', Category::$TYPES),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            $exists = Category::whereName($validator->getData()['name'])->exists();

            if ($exists) {
                $validator->errors()->add('name', 'Provided name is already in use.');
            }
        });
    }

    public function validated($key = null, $default = null)
    {
        return $this->validator->validated() + [
            'tenant_id' => $this->user()->tenant_id,
            'domain_id' => currentSubdomain(),
            'created_by' => auth()->id(),
        ];
    }
}
