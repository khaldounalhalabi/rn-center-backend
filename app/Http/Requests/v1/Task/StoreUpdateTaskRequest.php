<?php

namespace App\Http\Requests\v1\Task;

use App\Enums\PermissionEnum;
use App\Enums\RolesPermissionEnum;
use App\Enums\TaskLabelEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateTaskRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:now'],
            'label' => ['nullable', 'string', 'min:3', 'max:255', TaskLabelEnum::validationRule()],
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'users' => ['array', 'nullable'],
            'users.*' => ['numeric', Rule::in(User::role(RolesPermissionEnum::SECRETARY['role'])->select('id')->get()->pluck('id')->toArray())],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isAdmin() || can(PermissionEnum::TASKS_MANAGEMENT)) {
            $this->merge([
                'user_id' => user()->id,
            ]);
        }
    }
}
