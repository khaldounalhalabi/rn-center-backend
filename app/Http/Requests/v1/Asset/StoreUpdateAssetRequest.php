<?php

namespace App\Http\Requests\v1\Asset;

use App\Enums\AssetTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $needQuantityTypes = implode(',', AssetTypeEnum::needQuantity());
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:10000',
            'serial_number' => [
                'nullable',
                'string',
                'min:3',
                'required_if:type,' . AssetTypeEnum::ASSET->value,
                'unique:assets,serial_number',
                Rule::excludeIf(fn() => $this->input('type') != AssetTypeEnum::ASSET->value)
            ],
            'type' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
                Rule::in(AssetTypeEnum::getAllValues()),
                Rule::requiredIf(fn() => $this->isPost()),
                Rule::excludeIf(fn() => $this->isPut())
            ],
            'quantity' => ['nullable', 'numeric', 'required_if:type,' . $needQuantityTypes, 'min:0'],
            'purchase_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'quantity_unit' => ['string', 'nullable', 'required_if:type,' . $needQuantityTypes],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('type') == AssetTypeEnum::ASSET->value) {
            $this->merge([
                'quantity' => 1,
                'quantity_unit' => 'item',
            ]);
        }
    }
}
