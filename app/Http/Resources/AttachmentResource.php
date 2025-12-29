<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'filename' => $this->original_filename,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_human' => $this->human_size,
            'url' => $this->url,
            'is_image' => $this->isImage(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
