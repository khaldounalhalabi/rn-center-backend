<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Models\UserPlatform;
use App\Notifications\SendVerificationCode;
use App\Repositories\AddressRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PhoneNumberRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @extends BaseService<User>
 * @property UserRepository $repository
 */
class UserService extends BaseService
{
    use Makable;

    protected string $repositoryClass = UserRepository::class;

    private CustomerRepository $customerRepository;

    private PhoneNumberRepository $phoneNumberRepository;

    private AddressRepository $addressRepository;

    public function init(): void
    {
        $this->customerRepository = CustomerRepository::make();
        $this->phoneNumberRepository = PhoneNumberRepository::make();
        $this->addressRepository = AddressRepository::make();
    }

    /**
     * @param array         $data
     * @param string[]|null $roles
     * @param array         $relations
     * @return array{User , string , string}|User|null
     */
    public function updateUserDetails(array $data, ?array $roles = null, array $relations = []): array|User|null
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($roles && !$user->hasAnyRole($roles)) {
            return null;
        }

        if (isset($data['password']) && $data['password'] == "") {
            unset($data['password']);
        }

        /** @var User $user */
        $user = $this->repository->update($data, $user->id);

        if (isset($data['address'])) {
            $user->address()->updateOrCreate([
                ...$data['address'],
                'name'    => $data['address']['name'] ?? '{"en":"" , "ar":""}',
                'city_id' => $data['city_id'] ?? 1,
            ]);
        }

        if (isset($data['phone_numbers'])) {
            $user->phones()->delete();
            $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = auth()->login($user);

        /** @noinspection LaravelFunctionsInspection */
        $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 60 * 24 * 7))->refresh();

        return [$user->load($relations), $token, $refresh_token,];

    }

    /**
     * @throws RoleDoesNotExistException
     */
    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $user = $this->repository->update($data, $id);

        if (isset($data['address'])) {
            $user->address()->update($data['address']);
        }

        if (isset($data['phone_numbers'])) {
            $user->phones()->delete();
            $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);
        }

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user->load($relationships);
    }

    public function delete($id): ?bool
    {
        $user = $this->repository->find($id);

        if ($user->isAdmin()) {
            return null;
        }

        return parent::delete($id);
    }

    /**
     * @param array         $data
     * @param string[]|null $roles
     * @param array         $relations
     * @param array         $additionalData
     * @return User|Authenticatable|array{User , string , string}|null
     */
    public function login(array $data, ?array $roles = null, array $relations = [], array $additionalData = []): User|Authenticatable|array|null
    {
        $token = auth()->attempt([
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$token) {
            return null;
        }

        $user = auth()->user();

        if ($roles && !$user->hasAnyRole($roles)) {
            return null;
        }

        if (isset($data['fcm_token']) && $data['fcm_token']) {
            $this->clearFcmTokenFromOtherUsers($data['fcm_token']);
            $user->fcm_token = $data['fcm_token'];
            $user->save();
        }

        if ($user->isCustomer() && isset($data['platform'])) {
            $platform = UserPlatform::where('user_id', $user->id)
                ->firstOrCreate([
                    'user_id'      => $user->id,
                    'browser_type' => $data['platform']['browser_type'] ?? "Unknown",
                    'device_type'  => $data['platform']['device_type'] ?? "Unknown",
                    'ip'           => $data['platform']['ip'] ?? "Unknown",
                ]);

            //TODO::convert it to use otp
//            if ($user->hasVerifiedEmail()) {
//                $user->notify(new NewLoginEmailNotification(
//                    $platform->ip,
//                    $platform->device_type,
//                    $platform->browser_type
//                ));
//            }
        }

        foreach ($additionalData as $key => $value) {
            $user->{$key} = $value;
            $user->save();
        }

        /** @noinspection LaravelFunctionsInspection */
        $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 60 * 24 * 7))->refresh();

        return [$user->load($relations), $token, $refresh_token,];
    }

    /**
     * @param       $fcm_token
     * @return void
     */
    public function clearFcmTokenFromOtherUsers($fcm_token): void
    {
        $users = $this->repository->getByFcmToken($fcm_token);
        foreach ($users as $user) {
            $user->fcm_token = null;
            $user->save();
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $user = auth()->user();
        auth('api')->logout();
        $user->fcm_token = null;
        $user->save();
    }

    /**
     * @return array{User , string , string}|null
     */
    public function refreshToken(array $relations = []): ?array
    {
        try {
            $user = auth()->user();
            /** @noinspection LaravelFunctionsInspection */
            $token = auth()->setTTL(env('JWT_TTL', 10080))->refresh();
            /** @noinspection LaravelFunctionsInspection */
            $refresh_token = auth()->setTTL(env('JWT_REFRESH_TTL', 20160))->refresh();

            return [$user->load($relations), $token, $refresh_token];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array         $data
     * @param string[]|null $roles
     * @param array         $relations
     * @return array{User , string , string}
     * @throws RoleDoesNotExistException
     */
    public function register(array $data, ?array $roles = null, array $relations = []): array
    {
        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = $this->repository->create($data);

            if ($roles) {
                foreach ($roles as $role) {
                    $user->assignRole($role);
                }
            }

            if ($roles and in_array(RolesPermissionEnum::CUSTOMER['role'], $roles)) {
                $data = array_merge($data, ['user_id' => $user->id]);
                $this->customerRepository->create($data);
                $this->phoneNumberRepository->insert($data['phone_number'] ?? [], User::class, $user->id);

                if (isset($data['address'])) {
                    $this->addressRepository->create([
                        ...$data['address'],
                        'addressable_id'   => $user->id,
                        'addressable_type' => User::class,
                    ]);
                }

                if (isset($data['phone_number'])) {
                    $number = array_values($data['phone_number'])[0];
                    if ($number) {
                        PhoneNumberService::make()->requestNumberVerificationCode($number, $user);
                    }
                } else {
                    throw new Exception("Phone number is required in register customer");
                }

                DB::commit();
                return [$user->load($relations), null, null];
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $token = auth()->login($user);

            /** @noinspection LaravelFunctionsInspection */
            $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 60 * 24 * 7))->refresh();

            DB::commit();
            return [$user->load($relations), $token, $refresh_token,];
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function requestVerificationCode(User $user): void
    {
        $code = $this->generateUserVerificationCode();

        $user->notify(new SendVerificationCode(
            $code,
            'Verify Your Email',
            'Your Email Verification Code Is : '
        ));

        $user->verification_code = $code;
        $user->save();
    }

    /**
     * @return string
     */
    public function generateUserVerificationCode(): string
    {
        do {
            $code = sprintf('%06d', mt_rand(1, 999999));
            $temp_user = $this->getUserByPasswordResetCode($code);
        } while ($temp_user != null);
        return $code;
    }

    /**
     * @param            $token
     * @return User|null
     */
    public function getUserByPasswordResetCode($token): ?User
    {
        return $this->repository->getUserByPasswordResetCode($token);
    }

    /**
     * @param $verificationCode
     * @return bool
     */
    public function verifyCustomerEmail($verificationCode): bool
    {
        /** @var User $user */
        $user = $this->repository->getUserByVerificationCode($verificationCode);

        if (!$user) return false;

        if ($user->verification_code != $verificationCode) {
            return false;
        }

        $user->markEmailAsVerified();
        $user->verification_code = null;
        $user->save();

        return true;
    }

    /**
     * @param string $email
     * @return bool|null
     */
    public function passwordResetRequest(string $email): ?bool
    {
        $user = $this->getUserByEmail($email);

        if ($user) {
            $code = $this->generateUserVerificationCode();

            $user->reset_password_code = $code;
            $user->save();

            try {
                $user->notify(new SendVerificationCode(
                    $code,
                    'Reset Password Verification Code',
                    'Your Password Reset Code Is : '
                ));
            } catch (Exception) {
                return null;
            }

            return true;
        }

        return null;
    }

    /**
     * @param            $email
     * @return User|null
     */
    public function getUserByEmail($email): ?User
    {
        return $this->repository->getUserByEmail($email);
    }

    /**
     * @param string $emailResetCode
     * @param string $password
     * @return bool
     */
    public function passwordReset(string $emailResetCode, string $password): bool
    {
        $user = $this->getUserByPasswordResetCode($emailResetCode);

        if (!$user) return false;

        if ($user->updated_at->equalTo(now()->subMinutes(15))) {
            return false;
        }

        $user->password = $password;
        $user->reset_password_code = null;
        $user->save();

        return true;
    }

    /**
     * @param string[]|null $roles
     * @param array         $relations
     * @return User|Authenticatable|null
     */
    public function userDetails(?array $roles = null, array $relations = []): User|Authenticatable|null
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($roles && !$user->hasAnyRole($roles)) {
            return null;
        }

        return $user->load($relations);
    }

    public function toggleArchive($userId): ?string
    {
        /** @var User $user */
        $user = $this->repository->find($userId);
        if (!$user) {
            return null;
        }

        $user = $this->repository->update(["is_archived" => !$user->is_archived], $user);

        return $user->is_archived ? "archived" : "not_archived";
    }

    /**
     * @throws RoleDoesNotExistException
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        /** @var User $user */
        $user = $this->repository->create($data);

        if (isset($data['address'])) {
            $data['address']['addressable_id'] = $user->id;
            $data['address']['addressable_type'] = User::class;
            $this->addressRepository->create($data['address']);
        }

        if (isset($data['phone_numbers'])) {
            $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);
        }

        $user->assignRole($data['role'] ?? RolesPermissionEnum::CUSTOMER['role']);

        return $user->load($relationships);
    }

    /**
     * @param $userId
     * @return string|null
     */
    public function toggleBlockUser($userId): ?string
    {
        $user = $this->repository->find($userId);

        if (!$user) {
            return null;
        }

        $user = $this->repository->update([
            'is_blocked' => !$user->is_blocked,
        ], $user);

        return $user->is_blocked ? "blocked" : "not_blocked";
    }

    public function passwordResetRequestByPhone(string $phone): ?bool
    {
        $phoneNumber = PhoneNumberRepository::make()->getByPhone($phone);

        if ($phoneNumber) {
            $code = PhoneNumberService::make()->generateNumberVerificationCode();

            $phoneNumber->update([
                'verification_code' => $code,
            ]);

            try {
                SmsService::make()->sendVerificationCode($code, $phoneNumber->phone, $phoneNumber->phoneable_id);
            } catch (Exception) {
                return null;
            }

            return true;
        }

        return null;
    }

    public function passwordResetByPhone($phoneCode, $password): bool
    {
        $phone = PhoneNumberRepository::make()->getByVerificationCode($phoneCode);

        if (!$phone) {
            return false;
        }

        /** @var null|User $user */
        $user = $phone->phoneable_type == User::class ? $phone->phoneable : null;

        if (!$user) return false;

        if ($phone->updated_at->equalTo(now()->subMinutes(15))) {
            return false;
        }

        $user->password = $password;
        $user->save();

        $phone->verification_code = null;
        $phone->save();

        return true;
    }

    /**
     * @param array $data
     * @param array $relations
     * @param array $countable
     * @return array{User , string , string , PhoneNumber}|null
     */
    public function loginByPhone(array $data, array $relations = [], array $countable = []): ?array
    {
        $phoneNumber = PhoneNumberRepository::make()->getByPhone($data['phone_number'], ['phoneable']);

        if (!$phoneNumber) {
            return null;
        }

        /** @var User|null $user */
        $user = $phoneNumber->phoneable_type == User::class ? $phoneNumber->phoneable : null;

        if (!$user) {
            return null;
        }

        if (!Hash::check($data['password'], $user->password)) {
            return null;
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = auth()->login($user);
        /** @noinspection LaravelFunctionsInspection */
        $refreshToken = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 60 * 24 * 7))->refresh();

        return [
            $user->load($relations)->loadCount($countable),
            $token,
            $refreshToken,
            $phoneNumber,
        ];
    }
}
