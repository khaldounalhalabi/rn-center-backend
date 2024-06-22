<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\ModelHasPermission */
class PermissionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->pluck('permissions')->flatten()->toArray();
    }
}
