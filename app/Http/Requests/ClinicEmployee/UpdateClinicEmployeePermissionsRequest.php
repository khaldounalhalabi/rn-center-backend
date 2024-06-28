<?php

namespace App\Http\Requests\ClinicEmployee;

use App\Enums\RolesPermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClinicEmployeePermissionsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'permissions'   => 'array|required',
            'permissions.*' => ['string', Rule::in(array_keys(RolesPermissionEnum::CLINIC_EMPLOYEE['permissions']))]
        ];
    }
}
