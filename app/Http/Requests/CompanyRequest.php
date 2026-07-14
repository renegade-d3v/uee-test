<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CompanyRequest',
    required: ['name', 'edrpou', 'address'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'ТОВ Українська енергетична біржа', maxLength: 256),
        new OA\Property(property: 'edrpou', type: 'string', example: '37027819', maxLength: 10),
        new OA\Property(property: 'address', type: 'string', example: '01001, Україна, м. Київ, вул. Хрещатик, 44'),
    ],
    type: 'object',
)]
final class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:256',
            'edrpou' => 'required|string|max:10',
            'address' => 'required|string',
        ];
    }
}
