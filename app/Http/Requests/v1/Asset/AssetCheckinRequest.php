<?php

namespace App\Http\Requests\v1\Asset;

use App\Enums\AssetTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Asset;
use App\Models\User;
use App\Repositories\AssetRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetCheckinRequest extends FormRequest
{
    private Asset|null $asset;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->asset = AssetRepository::make()->find($this->input('asset_id'));
        if ($this->asset?->type == AssetTypeEnum::ASSET->value) {
            $this->merge([
                'quantity' => 1
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'numeric', Rule::exists('assets', 'id')],
            'user_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')
                    ->whereIn('id', User::role([RolesPermissionEnum::SECRETARY['role'], RolesPermissionEnum::DOCTOR['role']])
                        ->select('id')
                        ->get()
                        ->pluck('id')
                        ->toArray()
                    )
            ],
            'quantity' => [
                'nullable',
                'numeric',
                'gt:0',
                Rule::when($this->asset?->quantity, ["max:" . $this->asset?->quantity ?? 0]),
                Rule::requiredIf(in_array($this->asset?->type, AssetTypeEnum::needQuantity()))
            ],
            'checkin_condition' => [
                'numeric',
                'integer',
                'min:1',
                'max:10',
                'nullable',
                Rule::excludeIf(fn() => !in_array($this->asset?->type, AssetTypeEnum::needCheckout()))
            ],
            'expected_return_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:now',
                Rule::excludeIf(fn() => !in_array($this->asset?->type, AssetTypeEnum::needCheckout()))
            ],
        ];
    }
}
