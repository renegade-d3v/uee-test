<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Version',
    type: 'object',
    properties: [
        new OA\Property(property: 'version', type: 'integer', example: 2),
        new OA\Property(property: 'old_data', type: 'object', nullable: true, example: ['name' => 'Стара назва', 'edrpou' => '37027819', 'address' => 'Стара адреса']),
        new OA\Property(property: 'new_data', type: 'object', example: ['name' => 'Нова назва', 'edrpou' => '37027819', 'address' => 'Стара адреса']),
        new OA\Property(property: 'changes', type: 'object', nullable: true, example: ['name' => 'Нова назва']),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-07-14T08:04:19.000000Z'),
    ],
)]
/** @mixin Version */
final class VersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'version' => $this->version,
            'old_data' => $this->old_data,
            'new_data' => $this->new_data,
            'changes' => $this->changes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
