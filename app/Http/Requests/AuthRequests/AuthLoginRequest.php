<?php

namespace App\Http\Requests\AuthRequests;

use App\Rules\NotInBlocked;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
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
            'email'                 => ['required', 'email', 'exists:users,email', 'max:255', new NotInBlocked()],
            'password'              => 'required|min:8|max:255',
            'fcm_token'             => 'nullable|string|max:1000',
            'platform.device_type'  => 'nullable|string|max:255',
            'platform.browser_type' => 'nullable|string|max:255',
            'platform.ip_address'   => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'platform' => [
                'ip_address'   => $this->ip(),
                'browser_type' => str_replace(['Unknown-', '-Unknown'], '', Browser::browserFamily() . '-' . Browser::browserName()),
                'device_type'  => str_replace(['Unknown-', '-Unknown'], "", Browser::deviceType() . '-' .
                    Browser::deviceFamily() . '-' .
                    Browser::platformFamily() . '-' .
                    Browser::deviceModel() . '-' .
                    Browser::platformName()),
            ],
        ]);
    }
}
