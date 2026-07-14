<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\VersioningServiceInterface;
use App\Enums\CompanyStatusEnum;
use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CompanyService
{
    public function __construct(private VersioningServiceInterface $versioning,) {}

    /** @return array{status: CompanyStatusEnum, company_id: int, version: int}
     * @throws Throwable
     */
    public function createOrUpdate(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $company = Company::query()
                ->where('edrpou', $data['edrpou'])
                ->lockForUpdate()
                ->first();

            if (! $company) {
                $company = Company::query()->create($data);
                $version = $this->versioning->createInitialVersion($company);

                return [
                    'status' => CompanyStatusEnum::Created,
                    'company_id' => $company->id,
                    'version' => $version->version,
                ];
            }

            $old = Arr::only($company->getAttributes(), ['name', 'edrpou', 'address']);

            $company->fill($data);

            if (! $company->isDirty(['name', 'edrpou', 'address'])) {
                return [
                    'status' => CompanyStatusEnum::Duplicate,
                    'company_id' => $company->id,
                    'version' => $company->versions()->max('version'),
                ];
            }

            $new = array_merge($old, $company->getDirty());
            $company->save();

            $version = $this->versioning->createVersion(model: $company, old: $old, new: $new);

            return [
                'status' => CompanyStatusEnum::Updated,
                'company_id' => $company->id,
                'version' => $version->version,
            ];
        });
    }
}
