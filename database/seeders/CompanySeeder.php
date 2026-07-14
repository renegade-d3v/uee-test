<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\CompanyService;
use Illuminate\Database\Seeder;

final class CompanySeeder extends Seeder
{
    public function __construct(
        private readonly CompanyService $service,
    ) {}

    public function run(): void
    {
        $records = [
            [
                'name' => 'ТОВ Українська енергетична біржа',
                'edrpou' => '37027819',
                'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
            ],
            [
                'name' => 'ПАТ Енергоатом',
                'edrpou' => '24584661',
                'address' => '01032, Україна, м. Київ, вул. Назарівська, 3',
            ],
        ];

        foreach ($records as $data) {
            $this->service->createOrUpdate($data);
        }
    }
}
