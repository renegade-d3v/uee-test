<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VersionResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

final class CompanyVersionController extends Controller
{
    #[OA\Get(
        path: '/api/company/{edrpou}/versions',
        operationId: 'getCompanyVersions',
        summary: 'Get all versions of a company',
        tags: ['Company'],
        parameters: [
            new OA\Parameter(
                name: 'edrpou',
                in: 'path',
                required: true,
                description: 'Company EDRPOU code (8–10 characters)',
                schema: new OA\Schema(type: 'string', maxLength: 10, example: '37027819'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Company version history',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'company_id', type: 'integer', example: 5),
                        new OA\Property(property: 'edrpou', type: 'string', example: '37027819'),
                        new OA\Property(
                            property: 'versions',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Version'),
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Company not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Company not found.'),
                    ],
                ),
            ),
        ],
    )]
    public function index(string $edrpou): JsonResponse
    {
        $company = Company::query()->where('edrpou', $edrpou)->first();

        if (! $company) {
            return response()->json(['message' => 'Company not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'company_id' => $company->id,
            'edrpou' => $company->edrpou,
            'versions' => VersionResource::collection($company->versions),
        ]);
    }
}
