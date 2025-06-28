<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DicomFile implements ValidationRule
{
    /**
     * Run the validation rule.
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value->isValid()) {
            $fail('The file must be a valid DICOM file.');
        }

        // Check if the file has the expected DICOM extension
        if ($value->getClientOriginalExtension() !== 'dcm' && $value->getClientOriginalExtension() !== 'DCM') {
            $fail('The file must be a valid DICOM file.');
        }

        // Optionally, check the file header for DICOM magic number
        $fileContent = file_get_contents($value->getRealPath(), false, null, 0, 132);
        if (!strpos($fileContent, 'DICM')) {
            $fail('The file must be a valid DICOM file.');
        };
    }
}
