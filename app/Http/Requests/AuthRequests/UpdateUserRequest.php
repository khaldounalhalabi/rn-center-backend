<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\BloodGroupEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        $guard = request()->acceptsHtml() ? 'web' : 'api';

        $additional = [];
        if ($this->fullUrl() == route('api.customer.update.user.data')) {
            $additional = [
                'birth_date' => 'required|date|date_format:Y-m-d',
                'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues())
            ];
        }

        return [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|regex:/^09\d{8}$/|unique:users,phone,' . auth($guard)->user()?->id,
            'password' => 'nullable|min:8|confirmed',
            ...$additional
        ];
    }
}
