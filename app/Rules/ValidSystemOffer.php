<?php

namespace App\Rules;

use App\Models\SystemOffer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSystemOffer implements ValidationRule
{
    private ?int $customerId;

    public function __construct(?int $customerId = null)
    {
        if (auth()->user()?->isCustomer()) {
            $this->customerId = auth()->user()?->customer->id;
        } else {
            $this->customerId = null;
        }
    }

    /**
     * Run the validation rule.
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $offer = SystemOffer::find($value);

        if (!$offer) {
            $fail("Invalid Offer");
            return;
        }

        if (!$offer->isActive()) {
            $fail("The Provided Offer Has Expired");
            return;
        }

        if (!$this->customerId) {
            $fail("Invalid Offer");
            return;
        }

        if (
            !$offer->allow_reuse
            && DB::table('customer_system_offers')
                ->where('customer_id', $this->customerId)
                ->where('system_offer_id', $offer->id)
                ->count() > 1
        ) {
            $fail("You Used This Offer Before");
        }
    }
}
