<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Version
 *
 * @property int $id
 * @property string $versionable_type
 * @property int $versionable_id
 * @property int $version
 * @property array<string, mixed>|null $old_data
 * @property array<string, mixed> $new_data
 * @property array<string, mixed>|null $changes
 * @property Carbon $created_at
 * @property-read Model $versionable
 */
final class Version extends Model
{
    public const null UPDATED_AT = null;

    protected $fillable = ['version', 'old_data', 'new_data', 'changes'];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }
}
