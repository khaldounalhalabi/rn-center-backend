<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

/**
 * @see Permission
 * @property Collection<Permission> collection
 */
class PermissionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->pluck('name')->toArray();
    }
}
