<?php

namespace App\Http\Requests\v1\User;

use App\Enums\GenderEnum;
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');
        return [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'phone' => ['required', 'regex:/^09\d{8}$/', Rule::unique('users', 'phone')->when($this->isPut(), fn($rule) => $rule->ignore($userId))],
            'password' => ['string', 'min:8', 'max:20', 'nullable', 'confirmed', Rule::requiredIf(fn() => $this->isPost())],
            'gender' => ['required', 'string', GenderEnum::validationRule()],
            'formula_id' => ['nullable', 'numeric', 'exists:formulas,id'],
            'permissions' => 'array|nullable',
            'permissions.*' => ['string', PermissionEnum::validationRule(), Rule::excludeIf(fn() => !isAdmin())]
        ];
    }
}
