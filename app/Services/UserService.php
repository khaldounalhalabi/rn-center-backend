<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\User;
use App\Modules\SMS;
use App\Repositories\AttendanceRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerificationCodeRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<User>
 * @property UserRepository $repository
 */
class UserService extends BaseService
{
    use Makable;

    private string $guard = 'web';

    protected string $repositoryClass = UserRepository::class;

    /**
     * @param string $guard
     * @return void
     * @throws Exception
     */
    public function setGuard(string $guard = 'api'): void
    {
        if (!in_array($guard, array_keys(config('auth.guards')))) {
            throw new Exception("Undefined Guard : [$guard]");
        }

        $this->guard = $guard;
    }

    /**
     * @param array       $data
     * @param array       $relations
     * @param string|null $role
     * @return array{User , string , string}|User|null
     */
    public function updateUserDetails(array $data, array $relations = [], ?string $role = null): array|User|null
    {
        $user = auth($this->guard)->user();

        if (!$user) {
            return null;
        }

        if ($role && !$user->hasRole($role)) {
            return null;
        }

        $oldPhone = $user->phone;

        /** @var User $user */
        $user = $this->repository->update($data, $user->id);

        if ($oldPhone != $user->phone) {
            if ($this->sendVerificationCode($user)) {
                $user->unVerify();
            }
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = auth($this->guard)->login($user);

        if (!request()->acceptsHtml()) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $refresh_token = auth($this->guard)->setTTL(ttl: config('jwt.refresh_ttl'))->refresh();

            return [$user->load($relations), $token, $refresh_token,];
        }

        return $user->load($relations);
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
        $token = auth($this->guard)->attempt([
            'phone' => $data['phone'],
            'password' => $data['password'],
        ]);

        if (!$token) {
            return null;
        }

        $user = auth($this->guard)->user();

        if ($role && !$user->hasRole($role)) {
            return null;
        }

        if (count($additionalData)) {
            $user->update($additionalData);
        }

        if (isset($data['fcm_token'])) {
            FcmTokenRepository::make()->create([
                'token' => $data['fcm_token'],
                'user_id' => $user->id
            ]);
        }

        if (!request()->acceptsHtml()) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $refresh_token = auth($this->guard)->setTTL(ttl: config('jwt.refresh_ttl'))->refresh();

            return [$user->load($relations), $token, $refresh_token,];
        }

        return $user;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $user = auth($this->guard)->user();
        auth($this->guard)->logout();
        $user->save();
    }

    /**
     * @return array{User , string , string}|null
     */
    public function refreshToken(array $relations = []): ?array
    {
        try {
            $user = auth($this->guard)->user();
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $token = auth($this->guard)->setTTL(config('jwt.ttl'))->refresh();
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $refresh_token = auth($this->guard)->setTTL(config('jwt.refresh_ttl'))->refresh();

            return [$user->load($relations), $token, $refresh_token];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array       $data
     * @param array       $relations
     * @param string|null $role
     * @return array{User , string , string}|User
     * @throws RoleDoesNotExistException
     */
    public function register(array $data, array $relations = [], ?string $role = null): array|User
    {
        $user = $this->repository->create($data);

        if ($role) {
            $user->assignRole($role);
        }

        if (isset($data['fcm_token'])) {
            FcmTokenRepository::make()->create([
                'token' => $data['fcm_token'],
                'user_id' => $user->id
            ]);
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $token = auth($this->guard)->login($user);

        $this->sendVerificationCode($user);

        if ($role == RolesPermissionEnum::CUSTOMER['role']) {
            $data['user_id'] = $user->id;
            CustomerRepository::make()->create($data);
        }

        if (!request()->acceptsHtml()) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $refreshToken = auth($this->guard)->setTTL(ttl: config('jwt.refresh_ttl'))->refresh();

            return [$user->load($relations), $token, $refreshToken,];
        }

        return $user->load($relations);
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function passwordResetRequest(string $phone): bool
    {
        $user = $this->repository->getUserByPhone($phone);

        if (!$user) {
            return false;
        }

        return $this->sendVerificationCode($user);
    }

    /**
     * @param string $resetPasswordCode
     * @param string $password
     * @return bool
     */
    public function passwordReset(string $resetPasswordCode, string $password): bool
    {
        $code = VerificationCodeRepository::make()
            ->getActiveByCode($resetPasswordCode);

        if (!$code) {
            return false;
        }

        $user = $this->repository->getUserByPhone($code->phone);

        $this->repository->update([
            'password' => $password,
        ], $user);

        VerificationCodeRepository::make()
            ->update([
                'is_active' => false,
            ], $code);

        return true;
    }

    /**
     * @param array       $relations
     * @param string|null $role
     * @return User|Authenticatable|null
     */
    public function userDetails(array $relations = [], ?string $role = null): User|Authenticatable|null
    {
        $user = auth($this->guard)->user();

        if (!$user) {
            return null;
        }

        if ($role && !$user->hasRole($role)) {
            return null;
        }

        return $user->load($relations);
    }

    private function generateVerificationCode(): string
    {
        if (app()->environment('production')) {
            do {
                $code = sprintf('%06d', mt_rand(1, 999999));
                $tempCode = VerificationCodeRepository::make()->getActiveByCode($code);
            } while ($tempCode != null);

            return $code;
        }

        return "0000";
    }

    public function verifyUser(array $data, ?string $role = null): bool
    {
        $code = VerificationCodeRepository::make()->getActiveByCode($data['verification_code']);
        if (!$code) {
            return false;
        }

        $user = $this->repository->getUserByPhone($code->phone);

        if (!$user || !$user->hasRole($role)) {
            return false;
        }

        $user->verify();

        VerificationCodeRepository::make()->update([
            'is_active' => false,
        ], $code);

        return true;
    }

    /**
     * @param string $phone
     * @return void
     */
    private function inActivatePreviousCodes(string $phone): void
    {
        VerificationCodeRepository::make()
            ->globalQuery()
            ->where('phone', $phone)
            ->where('is_active', true)
            ->update([
                'is_active' => false
            ]);
    }

    public function sendVerificationCode(User $user): bool
    {
        $code = $this->generateVerificationCode();
        $sms = SMS::make()
            ->message(trans('site.your_verification_code_is', ['code' => $code]))
            ->to($user->universal_phone)
            ->send();

        if (!$sms->succeed()) {
            return false;
        }

        $this->inActivatePreviousCodes($user->phone);

        VerificationCodeRepository::make()
            ->create([
                'code' => $code,
                'phone' => $user->phone,
                'valid_until' => now()->addHours(3),
            ]);

        return true;
    }

    public function resendVerificationCode(string $phone, ?string $role = null): bool
    {
        $user = $this->repository->getUserByPhone($phone);
        if (!$user) {
            return false;
        }

        if ($role && !$user->hasRole($role)) {
            return false;
        }

        return $this->sendVerificationCode($user);
    }

    public function getWithAttendance(array $relations = [], array $countable = []): array
    {
        $date = Carbon::parse(request('attendance_at', now()));
        return [
            'attendance' => AttendanceRepository::make()->getByDateOrCreate($date),
            'users' => $this->repository->employees([
                'attendanceByDate',
                ...$relations
            ], $countable)
        ];
    }

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        /** @var User $user */
        $user = parent::store($data);
        $user->assignRole($data['role']);
        if (isset($data['permissions'])) {
            $user->givePermissionTo(collect($data['permissions']));
        }
        return $user->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $user = $this->repository->find($id);
        if (!$user) {
            return null;
        }

        $user = $this->repository->update($data, $user);
        if (isset($data['permissions'])) {
            $user->syncPermissions(collect($data['permissions']));
        } else {
            $user->permissions()->detach();
        }
        return $user->load($relationships, $countable);
    }

    public function getSecretaries(array $relations = [], array $countable = []): array
    {
        return $this->repository->getSecretaries($relations, $countable);
    }

    public function employees(array $relations = [], array $countable = []): ?array
    {
        return $this->repository->employees($relations, $countable);
    }
}
