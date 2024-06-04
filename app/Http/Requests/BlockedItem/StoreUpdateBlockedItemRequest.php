<?php

namespace App\Http\Requests\BlockedItem;

use App\Enums\BlockTypeEnum;
use App\Models\BlockedItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class StoreUpdateBlockedItemRequest extends FormRequest
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
    #[ArrayShape(['type' => "string[]", 'value' => "string[]"])]
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'type'  => ['required', 'string', 'min:3', 'max:255', Rule::in(BlockTypeEnum::getAllValues())],
                'value' => ['required', 'string', 'min:3', 'max:255', 'unique:blocked_items,value'],
            ];
        }

        $blocked = BlockedItem::find(request()->route('blocked_item'));
        return [
            'type'  => ['nullable', 'string', 'min:3', 'max:255', Rule::in(BlockTypeEnum::getAllValues())],
            'value' => ['nullable', 'string', 'min:3', 'max:255', 'unique:blocked_items,value,' . $blocked?->id,],
        ];
    }
}
