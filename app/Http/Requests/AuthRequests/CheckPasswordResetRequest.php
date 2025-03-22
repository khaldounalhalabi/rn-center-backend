<?php

namespace App\Http\Requests\AuthRequests;

use App\Services\UserService;
use Illuminate\Foundation\Http\FormRequest;

class CheckPasswordResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'reset_password_code' => [
                'required',
                'string',
                'max:10',
                function ($attribute, $value, $fail) {
                    $user = UserService::make()->getUserByPasswordResetCode($value);

                    if (!$user) {
                        $fail(__('site.code_incorrect'));
                    }

                    if (!$user?->reset_code_valid_until?->isAfter(now())) {
                        $fail(__('site.code_expired'));
                    }
                },
            ],
        ];
    }
}
