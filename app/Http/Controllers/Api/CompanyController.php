<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Schema(
    schema: 'CompanyStatusResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'created', enum: ['created', 'updated', 'duplicate']
        ),
        new OA\Property(property: 'company_id', type: 'integer', example: 5),
        new OA\Property(property: 'version', type: 'integer', example: 1),
    ],
    type: 'object',
)]
final class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $service,
    ) {}

    #[OA\Post(
        path: '/api/company',
        operationId: 'storeCompany',
        description: 'Creates a new company (status=created), updates it if any field changed (status=updated), or returns a duplicate (status=duplicate). Runs inside a transaction with row-level locking.',
        summary: 'Create or update a company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CompanyRequest'),
        ),
        tags: ['Company'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Company created',
                content: new OA\JsonContent(ref: '#/components/schemas/CompanyStatusResponse'),
            ),
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Company updated or duplicate (no changes)',
                content: new OA\JsonContent(ref: '#/components/schemas/CompanyStatusResponse'),
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The name field is required.'),
                        new OA\Property(property: 'errors', type: 'object', example: ['name' => ['The name field is required.']]),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function store(CompanyRequest $request): JsonResponse
    {
        return response()->json($this->service->createOrUpdate($request->validated()));
    }
}
