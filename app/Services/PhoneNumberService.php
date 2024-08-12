<?php

namespace App\Services;

use App\Models\PhoneNumber;
use App\Models\User;
use App\Repositories\PhoneNumberRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;

/**
 * @extends BaseService<PhoneNumber>
 * @property PhoneNumberRepository $repository
 */
class PhoneNumberService extends BaseService
{
    use Makable;

    protected string $repositoryClass = PhoneNumberRepository::class;


    /**
     * @param string $phone
     * @param User   $user
     * @return void
     */
    public function requestNumberVerificationCode(string $phone, User $user): void
    {
        $code = $this->generateNumberVerificationCode();

        SmsService::make()->sendVerificationCode($code, $phone, $user->id);

        $phoneNumber = $this->repository->getByPhone($phone);
        $phoneNumber->update([
            'verification_code' => $code,
        ]);
    }

    /**
     * @return string
     */
    public function generateNumberVerificationCode(): string
    {
        do {
            $code = app()->environment('local') ? "0000" : sprintf('%06d', mt_rand(1, 999999));
            $tempNumber = $this->repository->getByVerificationCode($code);
        } while ($tempNumber != null);
        return $code;
    }

    public function getByPhone(string $phone): ?PhoneNumber
    {
        return $this->repository->getByPhone($phone);
    }

    /**
     * @param $verificationCode
     * @return bool
     */
    public function verify($verificationCode): bool
    {
        /** @var User $user */
        $phone = PhoneNumberRepository::make()->getByVerificationCode($verificationCode);

        if (!$phone) {
            return false;
        }

        $phone->update([
            'is_verified'       => true,
            'verification_code' => null,
        ]);

        return true;
    }
}
