<?php

namespace App\Http\Requests\v1\Clinic;

use App\Enums\GenderEnum;
use App\Repositories\ClinicRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateClinicRequest extends FormRequest
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
        $userId = isAdmin()
            ? ClinicRepository::make()->find(request()->route('clinic'))?->user_id
            : user()->id;
        return [
            'appointment_cost' => 'required|numeric|min:0',
            'max_appointments' => 'required|numeric|integer|min:2',
            'working_start_year' => 'required|date_format:Y',

            'user' => 'array|required',
            'user.first_name' => 'required|string|min:3|max:255',
            'user.last_name' => 'required|string|min:3|max:255',
            'user.phone' => ['required', 'regex:/^09\d{8}$/', Rule::unique('users', 'phone')->when($this->method() == 'PUT', fn($rule) => $rule->ignore($userId))],
            'user.password' => 'string|min:8|max:20|required|confirmed',
            'user.gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
            'user.formula_id' => ['nullable', 'numeric', 'exists:formulas,id'],

            'speciality_ids' => 'array|required',
            'speciality_ids.*' => 'required|numeric|exists:specialities,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'speciality_ids.*' => 'speciality',
        ];
    }
}
