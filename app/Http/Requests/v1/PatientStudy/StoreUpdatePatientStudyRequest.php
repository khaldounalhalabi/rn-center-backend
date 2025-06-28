<?php

namespace App\Http\Requests\v1\PatientStudy;

use App\Rules\DicomFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePatientStudyRequest extends FormRequest
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
            'dicom_files' => 'required|array|min:1',
            'dicom_files.*' => ['file', new DicomFile()],
            'customer_id' => 'numeric|exists:customers,id',
            'title' => 'required|string|max:500|min:2'
        ];
    }
}
