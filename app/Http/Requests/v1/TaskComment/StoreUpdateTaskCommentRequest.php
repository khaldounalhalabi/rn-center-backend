<?php

namespace App\Http\Requests\v1\TaskComment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateTaskCommentRequest extends FormRequest
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
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'task_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->isPost()),
                Rule::excludeIf(fn() => $this->isPut()),
                'numeric',
                'exists:tasks,id'
            ],
            'comment' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => user()->id,
        ]);
    }
}
