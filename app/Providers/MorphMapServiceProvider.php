<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class MorphMapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $map = [];
        foreach (File::allFiles(app_path('Models')) as $file) {
            $class = 'App\\Models\\'.str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
                continue;
            }

            $name = Str::kebab(class_basename($class));
            $map[$name] = $class;
        }

        Relation::enforceMorphMap($map);
    }
}
