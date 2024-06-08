<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\User;
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
        parent::__construct();
        $this->customerRepository = CustomerRepository::make();
        $this->phoneNumberRepository = PhoneNumberRepository::make();
        $this->addressRepository = AddressRepository::make();
    }

    /**
     * @param array       $data
     * @param string|null $role
     * @param array       $relations
     * @return array{User , string , string}|User|null
     */
    public function updateUserDetails(array $data, ?string $role = null, array $relations = []): array|User|null
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($role && !$user->hasRole($role)) {
            return null;
        }

        /** @var User $user */
        $user = $this->repository->update($data, $user->id);

        $token = auth()->login($user);

        $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 20160))->refresh();

        return [$user->load($relations), $token, $refresh_token,];

    }

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

        if ($user->hasRole(RolesPermissionEnum::ADMIN['role'])) {
            return null;
        }

        return parent::delete($id);
    }

    /**
     * @param array       $data
     * @param string|null $role
     * @param array       $relations
     * @param array       $additionalData
     * @return User|Authenticatable|array{User , string , string}|null
     */
    public function login(array $data, ?string $role = null, array $relations = [], array $additionalData = []): User|Authenticatable|array|null
    {
        $token = auth()->attempt([
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$token) {
            return null;
        }

        $user = auth()->user();

        if ($role && !$user->hasRole($role)) {
            return null;
        }

        if (isset($data['fcm_token']) && $data['fcm_token']) {
            $this->clearFcmTokenFromOtherUsers($data['fcm_token']);
            $user->fcm_token = $data['fcm_token'];
            $user->save();
        }

        foreach ($additionalData as $key => $value) {
            $user->{$key} = $value;
            $user->save();
        }

        $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 20160))->refresh();

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
            $token = auth()->setTTL(env('JWT_TTL', 10080))->refresh();
            $refresh_token = auth()->setTTL(env('JWT_REFRESH_TTL', 20160))->refresh();

            return [$user->load($relations), $token, $refresh_token];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array       $data
     * @param string|null $role
     * @param array       $relations
     * @return array{User , string , string}
     * @throws RoleDoesNotExistException
     */
    public function register(array $data, ?string $role = null, array $relations = []): array
    {
        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = $this->repository->create($data);

            if ($role) {
                $user->assignRole($role);
            }

            if ($role and $role == RolesPermissionEnum::CUSTOMER['role']) {
                $data = array_merge($data, ['user_id' => $user->id]);
                $this->customerRepository->create($data);
                $this->phoneNumberRepository->insert($data['phone_number'] ?? [], User::class, $user->id);

                if (isset($data['address'])) {
                    $this->addressRepository->create([
                        ...$data['address'],
                        'addressable_id'   => $user->id,
                        'addressable_type' => User::class
                    ]);
                }

                $this->requestVerificationCode($user);

                DB::commit();
                return [$user->load($relations), null, null];
            }

            $token = auth()->login($user);

            $refresh_token = auth()->setTTL(ttl: env('JWT_REFRESH_TTL', 20160))->refresh();

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

        if ($user->verification_code != $verificationCode) return false;

        $user->email_verified_at = now();
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
     * @param string $reset_password_code
     * @param string $password
     * @return bool
     */
    public function passwordReset(string $reset_password_code, string $password): bool
    {
        $user = $this->getUserByPasswordResetCode($reset_password_code);

        if (!$user) return false;

        if ($user->updated_at->addMinutes(10)->equalTo(now())) {
            return false;
        }

        $user->password = $password;
        $user->reset_password_code = null;
        $user->save();

        return true;
    }

    /**
     * @param string|null $role
     * @param array       $relations
     * @return User|Authenticatable|null
     */
    public function userDetails(?string $role = null, array $relations = []): User|Authenticatable|null
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($role && !$user->hasRole($role)) {
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

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        /** @var User $user */
        $user = $this->repository->create($data);

        $data['address']['addressable_id'] = $user->id;
        $data['address']['addressable_type'] = User::class;
        $this->addressRepository->create($data['address']);

        $this->phoneNumberRepository->insert($data['phone_numbers'], User::class, $user->id);

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
            'is_blocked' => !$user->is_blocked
        ], $user);

        return $user->is_blocked ? "blocked" : "not_blocked";
    }
}
