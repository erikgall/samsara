<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Form\FormTemplate;
use Samsara\Data\Form\FormSubmission;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * Forms resource for the Samsara API.
 *
 * Provides access to form submission and template endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<FormSubmission>
 */
class FormsResource extends Resource
{
    /** @use CreatesEntities<FormSubmission> */
    use CreatesEntities;

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

        return $this->mapToEntity($this->decodeJsonData($response, $this->endpoint));
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

        return $this->decodeJsonData($response, $endpoint);
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

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<FormSubmission>
     */
    public function query(): Builder
    {
        return $this->newQuery()->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for form submissions.
     *
     * @return Builder<FormSubmission>
     */
    public function submissions(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for form submissions stream.
     *
     * @return Builder<FormSubmission>
     */
    public function submissionsStream(): Builder
    {
        return $this->newQuery('/form-submissions/stream')
            ->withoutDefaultPageSize()
            ->usingEntity(FormSubmission::class);
    }

    /**
     * Get all form templates.
     *
     * @return Builder<FormTemplate>
     */
    public function templates(): Builder
    {
        return $this->newQuery('/form-templates')
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

        return $this->mapToEntity($this->decodeJsonData($response, $this->endpoint));
    }
}
