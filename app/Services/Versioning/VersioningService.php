<?php

declare(strict_types=1);

namespace App\Services\Versioning;

use App\Contracts\VersioningServiceInterface;
use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

final class VersioningService implements VersioningServiceInterface
{
    public function createInitialVersion(Model $model): Version
    {
        /** @var Version */
        return $model->versions()->create([
            'version' => 1,
            'old_data' => null,
            'new_data' => $this->versionableAttributes($model),
            'changes' => null,
        ]);
    }

    public function createVersion(Model $model, array $old, array $new): Version
    {
        $next = ($model->versions()->max('version') ?? 0) + 1;
        $changes = array_diff_assoc($new, $old);

        /** @var Version */
        return $model->versions()->create([
            'version' => $next,
            'old_data' => $old,
            'new_data' => $new,
            'changes' => $changes ?: null,
        ]);
    }

    private function versionableAttributes(Model $model): array
    {
        $keys = method_exists($model, 'versionedAttributes')
            ? $model->versionedAttributes()
            : array_keys($model->getAttributes());

        return Arr::only($model->getAttributes(), $keys);
    }
}
