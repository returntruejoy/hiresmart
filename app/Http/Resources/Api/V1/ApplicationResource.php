<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_post_id' => $this->job_post_id,
            'candidate_id' => $this->candidate_id,
            'status' => $this->status,
            'applied_at' => $this->created_at,
            'candidate' => new UserResource($this->whenLoaded('candidate')),
        ];
    }
} 