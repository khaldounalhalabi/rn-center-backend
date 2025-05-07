<?php

namespace App\Traits;

use App\Exceptions\RoleDoesNotExistException;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * @mixin Model
 */
trait HasRoles
{
    use HasPermissions;

    /**
     * @param string $roleName
     * @return $this
     * @throws RoleDoesNotExistException
     */
    public function assignRole(string $roleName): static
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            throw new RoleDoesNotExistException($roleName);
        }

        $this->roles()->attach($role);

        return $this;
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->morphToMany(Role::class, 'modelsHasRole', 'model_has_roles');
    }

    /**
     * @param string $roleName
     * @return $this
     * @throws RoleDoesNotExistException
     */
    public function removeRole(string $roleName): static
    {
        $role = Role::getByName($roleName);

        if (!$role) {
            throw new RoleDoesNotExistException($roleName);
        }

        $this->roles()->detach($role);
        return $this;
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * @param string[] $roles
     * @return bool
     */
    public function hasAnyRole(array $roles = []): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * @param Builder         $query
     * @param string|string[] $roleName
     * @return void
     */
    public function scopeByRole(Builder $query, string|array $roleName): void
    {
        $query->whereHas('roles', function (Builder $q) use ($roleName) {
            $q->whereIn('name', Arr::wrap($roleName));
        });
    }
}
