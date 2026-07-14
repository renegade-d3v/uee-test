<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Version;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVersions
{
    public function versions(): MorphMany
    {
        return $this->morphMany(Version::class, 'versionable')->orderBy('version');
    }

    public function versionedAttributes(): array
    {
        return $this->getFillable();
    }
}
