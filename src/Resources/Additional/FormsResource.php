<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Form\FormTemplate;
use Samsara\Data\Form\FormSubmission;

/**
 * Forms resource for the Samsara API.
 *
 * Provides access to form submission and template endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FormsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/form-submissions';

    /**
     * The entity class for this resource.
     *
     * @var class-string<FormSubmission>
     */
    protected string $entity = FormSubmission::class;

    /**
     * Create a new form submission.
     *
     * @param  array<string, mixed>  $data
     */
    public function createSubmission(array $data): FormSubmission
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($this->endpoint, $data),
            $this->endpoint
        );

        /** @var FormSubmission */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Export form submissions as PDF.
     *
     * @return array<string, mixed>
     */
    public function exportPdf(string $id): array
    {
        $endpoint = '/form-submissions/pdf-exports';
        $response = $this->executeSdkRequest(
            fn () => $this->client()
                ->withQueryParameters(['id' => $id])
                ->post($endpoint),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * Get a PDF export by ID.
     *
     * @return array<string, mixed>
     */
    public function getPdfExport(string $id): array
    {
        $endpoint = '/form-submissions/pdf-exports';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, ['pdfId' => $id]),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Get a query builder for form submissions.
     */
    public function submissions(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for form submissions stream.
     */
    public function submissionsStream(): Builder
    {
        return $this->createBuilderWithEndpoint('/form-submissions/stream')
            ->withoutDefaultPageSize()
            ->usingEntity(FormSubmission::class);
    }

    /**
     * Get all form templates.
     */
    public function templates(): Builder
    {
        return $this->createBuilderWithEndpoint('/form-templates')
            ->withoutDefaultPageSize()
            ->usingEntity(FormTemplate::class);
    }

    /**
     * Update a form submission.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateSubmission(string $id, array $data): FormSubmission
    {
        $payload = [...$data, 'id' => $id];
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($this->endpoint, $payload),
            $this->endpoint
        );

        /** @var FormSubmission */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['create'];
    }
}
