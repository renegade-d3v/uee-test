<?php

declare(strict_types=1);

use App\Models\Company;
use App\Services\Versioning\VersioningService;

beforeEach(function (): void {
    $this->versioning = app(VersioningService::class);

    $this->company = Company::factory()->create([
        'name' => 'ТОВ Українська енергетична біржа',
        'edrpou' => '37027819',
        'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
    ]);

    $this->versioning->createInitialVersion($this->company);
});

it('returns 404 for unknown edrpou', function (): void {
    $this->getJson('/api/company/00000000/versions')->assertNotFound();
});

it('returns company_id and edrpou in response', function (): void {
    $this->getJson('/api/company/37027819/versions')
        ->assertOk()
        ->assertJson([
            'company_id' => $this->company->id,
            'edrpou' => '37027819',
        ]);
});

it('returns versions sorted by version number', function (): void {
    $this->company->update(['name' => 'Нова назва']);
    $this->versioning->createVersion(
        $this->company,
        ['name' => 'ТОВ Українська енергетична біржа', 'edrpou' => '37027819', 'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44'],
        ['name' => 'Нова назва', 'edrpou' => '37027819', 'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44'],
    );

    $this->getJson('/api/company/37027819/versions')
        ->assertOk()
        ->assertJsonCount(2, 'versions')
        ->assertJsonPath('versions.0.version', 1)
        ->assertJsonPath('versions.1.version', 2);
});

it('version 1 has null old_data and non-null new_data', function (): void {
    $this->getJson('/api/company/37027819/versions')
        ->assertOk()
        ->assertJsonPath('versions.0.old_data', null)
        ->assertJsonPath('versions.0.new_data.name', 'ТОВ Українська енергетична біржа');
});

it('update version has old_data, new_data and changes', function (): void {
    $this->company->update(['name' => 'Нова назва']);
    $this->versioning->createVersion(
        $this->company,
        ['name' => 'ТОВ Українська енергетична біржа', 'edrpou' => '37027819', 'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44'],
        ['name' => 'Нова назва', 'edrpou' => '37027819', 'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44'],
    );

    $this->getJson('/api/company/37027819/versions')
        ->assertOk()
        ->assertJsonPath('versions.1.old_data.name', 'ТОВ Українська енергетична біржа')
        ->assertJsonPath('versions.1.new_data.name', 'Нова назва')
        ->assertJsonPath('versions.1.changes.name', 'Нова назва');
});
