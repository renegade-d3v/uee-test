<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasVersions;
use Carbon\Carbon;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 *
 * @property int $id
 * @property string $name
 * @property string $edrpou
 * @property string $address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Version> $versions
 */
final class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, HasVersions;

    protected $fillable = ['name', 'edrpou', 'address'];

    public function versionedAttributes(): array
    {
        return ['name', 'edrpou', 'address'];
    }
}
