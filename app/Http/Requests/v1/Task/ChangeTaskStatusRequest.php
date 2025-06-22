<?php

namespace App\Http\Requests\v1\Task;

use App\Enums\TaskStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeTaskStatusRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => 'required|numeric|exists:tasks,id',
            'status' => ['string', 'required', TaskStatusEnum::validationRule()]
        ];
    }
}
