<?php

declare(strict_types=1);

use App\Enums\CompanyStatusEnum;
use App\Models\Company;
use App\Services\Versioning\VersioningService;

beforeEach(function (): void {
    $this->payload = [
        'name' => 'ТОВ Українська енергетична біржа',
        'edrpou' => '37027819',
        'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
    ];

    $this->versionService = app(VersioningService::class);
});

it('creates a new company and returns 200', function (): void {
    $this->postJson('/api/company', $this->payload)
        ->assertOk()
        ->assertJson([
            'status' => CompanyStatusEnum::Created->value,
            'version' => 1,
        ]);
});

it('creates version 1 with new_data and null old_data on first create', function (): void {
    $this->postJson('/api/company', $this->payload);

    $company = Company::query()->first();

    expect($company->versions)->toHaveCount(1);

    $version = $company->versions->first();

    expect($version->version)->toBe(1)
        ->and($version->old_data)->toBeNull()
        ->and($version->new_data['name'])->toBe($this->payload['name'])
        ->and($version->changes)->toBeNull();
});

it('updates company when name changes', function (): void {
    $company = Company::factory()->create($this->payload);
    $this->versionService->createInitialVersion($company);

    $updated = array_merge($this->payload, ['name' => 'ТОВ УЕБ (оновлено)']);

    $this->postJson('/api/company', $updated)
        ->assertOk()
        ->assertJson(['status' => CompanyStatusEnum::Updated->value, 'version' => 2]);

    expect($company->fresh()->name)->toBe('ТОВ УЕБ (оновлено)');
});

it('updates company when address changes', function (): void {
    $company = Company::factory()->create($this->payload);
    $this->versionService->createInitialVersion($company);

    $this->postJson('/api/company', array_merge($this->payload, ['address' => 'Нова адреса']))
        ->assertOk()
        ->assertJson(['status' => CompanyStatusEnum::Updated->value]);

    expect($company->fresh()->address)->toBe('Нова адреса');
});

it('saves old_data and new_data on update', function (): void {
    $company = Company::factory()->create($this->payload);
    $this->versionService->createInitialVersion($company);

    $this->postJson('/api/company', array_merge($this->payload, ['name' => 'Нова назва']));

    $v2 = $company->versions()->where('version', 2)->first();

    expect($v2->old_data['name'])->toBe($this->payload['name'])
        ->and($v2->new_data['name'])->toBe('Нова назва')
        ->and($v2->changes)->toHaveKey('name');
});

it('returns duplicate and does not create a new version when data is identical', function (): void {
    $company = Company::factory()->create($this->payload);
    $this->versionService->createInitialVersion($company);

    $this->postJson('/api/company', $this->payload)
        ->assertOk()
        ->assertJson(['status' => CompanyStatusEnum::Duplicate->value, 'version' => 1]);

    expect($company->versions()->count())->toBe(1);
});

it('does not update updated_at on duplicate', function (): void {
    $company = Company::factory()->create($this->payload);
    $this->versionService->createInitialVersion($company);

    $before = $company->updated_at;

    $this->travel(1)->seconds();

    $this->postJson('/api/company', $this->payload);

    expect($company->fresh()->updated_at->eq($before))->toBeTrue();
});

it('validates name is required', function (): void {
    $this->postJson('/api/company', array_merge($this->payload, ['name' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates name max length of 256', function (): void {
    $this->postJson('/api/company', array_merge($this->payload, ['name' => str_repeat('а', 257)]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates edrpou is required', function (): void {
    $this->postJson('/api/company', array_merge($this->payload, ['edrpou' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['edrpou']);
});

it('validates edrpou max length of 10', function (): void {
    $this->postJson('/api/company', array_merge($this->payload, ['edrpou' => '12345678901']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['edrpou']);
});

it('validates address is required', function (): void {
    $this->postJson('/api/company', array_merge($this->payload, ['address' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['address']);
});

it('returns 422 with json errors structure', function (): void {
    $this->postJson('/api/company', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['message', 'errors']);
});

it('cannot create two companies with the same edrpou', function (): void {
    $this->postJson('/api/company', $this->payload)->assertOk();
    $this->postJson('/api/company', $this->payload)->assertOk();

    expect(Company::query()->count())->toBe(1);
});
