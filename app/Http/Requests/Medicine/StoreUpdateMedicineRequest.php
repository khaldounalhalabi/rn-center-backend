<?php

namespace App\Http\Requests\Medicine;

use App\Enums\MedicineStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateMedicineRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:5000',
            'quantity' => 'required|numeric|min:0',
            'status' => ['required', 'string', Rule::in(MedicineStatusEnum::getAllValues())],
            'barcode' => ['nullable', 'max:100', 'string', Rule::unique('medicines', 'barcode')->when(
                $this->isPut(),
                fn($rule) => $rule->ignore($this->route('medicine'))
            )]
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isDoctor()) {
            $this->merge([
                'status' => MedicineStatusEnum::OUT_OF_STOCK->value,
            ]);
        }

        if (intval($this->input('quantity')) > 0) {
            $this->merge([
                'status' => MedicineStatusEnum::EXISTS->value,
            ]);
        } else {
            $this->merge([
                'status' => MedicineStatusEnum::OUT_OF_STOCK->value,
            ]);
        }
    }
}
