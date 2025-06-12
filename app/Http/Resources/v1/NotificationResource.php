<?php

namespace App\Http\Resources\v1;

use App\Modules\Notification\App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Notification
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'notifiable_id' => $this->notifiable_id,
            'data' => $this->data,
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
            //            'is_handled' => $this->is_handled,
            //            'model_id' => $this->model_id,
            //            'model_type' => $this->model_type,
            'resource' => $this['resource'],
            'resource_id' => $this->resource_id,
            'notifiable_type' => $this->notifiable_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
