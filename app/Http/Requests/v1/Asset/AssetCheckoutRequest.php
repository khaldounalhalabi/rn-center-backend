<?php

namespace App\Http\Requests\v1\Asset;

use App\Enums\AssetTypeEnum;
use App\Models\UserAsset;
use App\Repositories\UserAssetRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetCheckoutRequest extends FormRequest
{
    private UserAsset|null $userAsset;

    protected function prepareForValidation(): void
    {
        $this->userAsset = UserAssetRepository::make()->getAssignedByAssetAndUser($this->input('asset_id'), $this->input('user_id'), ['asset']);
        if ($this->userAsset?->asset?->type == AssetTypeEnum::ASSET->value) {
            $this->merge([
                'quantity' => 1
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'asset_id' => [
                'required',
                'numeric',
                Rule::exists('assets', 'id')->whereIn('type', AssetTypeEnum::needCheckout())
            ],
            'user_id' => [
                'required',
                'numeric',
                Rule::exists('user_assets', 'user_id')->where(fn(Builder $query) => $query->where('quantity', '>', 0))
            ],
            'quantity' => [
                'nullable',
                'numeric',
                'gt:0',
                Rule::when($this->userAsset?->quantity, [
                    "max:" . $this->userAsset?->quantity ?? 1
                ]),
                Rule::requiredIf(fn() => $this->userAsset?->asset?->type == AssetTypeEnum::ACCESSORIES->value)
            ],
            'checkout_condition' => ['nullable', 'min:0', 'max:10', 'numeric', 'integer']
        ];
    }
}
