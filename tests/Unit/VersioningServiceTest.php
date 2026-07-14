<?php

declare(strict_types=1);

use App\Models\Company;
use App\Services\Versioning\VersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = new VersioningService();

    $this->company = Company::factory()->create([
        'name' => 'ТОВ Тест',
        'edrpou' => '12345678',
        'address' => 'Київ, вул. Тестова, 1',
    ]);
});

it('creates version 1 with correct number', function (): void {
    $version = $this->service->createInitialVersion($this->company);

    expect($version->version)->toBe(1);
});

it('createInitialVersion sets old_data to null', function (): void {
    $version = $this->service->createInitialVersion($this->company);

    expect($version->old_data)->toBeNull();
});

it('createInitialVersion saves versionable attributes in new_data', function (): void {
    $version = $this->service->createInitialVersion($this->company);

    expect($version->new_data)->toMatchArray([
        'name' => 'ТОВ Тест',
        'edrpou' => '12345678',
        'address' => 'Київ, вул. Тестова, 1',
    ]);
});

it('createInitialVersion sets changes to null', function (): void {
    $version = $this->service->createInitialVersion($this->company);

    expect($version->changes)->toBeNull();
});

it('createVersion increments version number', function (): void {
    $this->service->createInitialVersion($this->company);

    $v2 = $this->service->createVersion(
        $this->company,
        ['name' => 'ТОВ Тест', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'],
        ['name' => 'ТОВ Тест Новий', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'],
    );

    expect($v2->version)->toBe(2);
});

it('createVersion saves old_data correctly', function (): void {
    $this->service->createInitialVersion($this->company);

    $oldData = ['name' => 'ТОВ Тест', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'];
    $newData = ['name' => 'ТОВ Тест Новий', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'];

    $v2 = $this->service->createVersion($this->company, $oldData, $newData);

    expect($v2->old_data)->toMatchArray($oldData);
});

it('createVersion saves new_data correctly', function (): void {
    $this->service->createInitialVersion($this->company);

    $newData = ['name' => 'ТОВ Тест Новий', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'];

    $v2 = $this->service->createVersion(
        $this->company,
        ['name' => 'ТОВ Тест', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'],
        $newData,
    );

    expect($v2->new_data)->toMatchArray($newData);
});

it('createVersion saves only changed fields in changes', function (): void {
    $this->service->createInitialVersion($this->company);

    $v2 = $this->service->createVersion(
        $this->company,
        ['name' => 'ТОВ Тест', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'],
        ['name' => 'ТОВ Тест Новий', 'edrpou' => '12345678', 'address' => 'Київ, вул. Тестова, 1'],
    );

    expect($v2->changes)->toHaveKey('name')
        ->and($v2->changes)->not->toHaveKey('edrpou')
        ->and($v2->changes)->not->toHaveKey('address');
});

it('persists version to database', function (): void {
    $this->service->createInitialVersion($this->company);

    $this->assertDatabaseHas('versions', [
        'versionable_type' => Company::class,
        'versionable_id' => $this->company->id,
        'version' => 1,
    ]);
});
