<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Version;
use Illuminate\Database\Eloquent\Model;

interface VersioningServiceInterface
{
    public function createInitialVersion(Model $model): Version;

    public function createVersion(Model $model, array $old, array $new): Version;
}
