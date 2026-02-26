<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
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
        $response = $this->client()->post('/form-submissions', $data);

        $this->handleError($response);

        /** @var FormSubmission */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Export form submissions as PDF.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function exportPdf(array $data): array
    {
        $response = $this->client()->post('/form-submissions/pdf-exports', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get a PDF export by ID.
     *
     * @return array<string, mixed>
     */
    public function getPdfExport(string $id): array
    {
        $response = $this->client()->get("/form-submissions/pdf-exports/{$id}");

        $this->handleError($response);

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
        return $this->createBuilderWithEndpoint('/form-submissions/stream');
    }

    /**
     * Get all form templates.
     *
     * @return EntityCollection<int, \Samsara\Data\Entity>
     */
    public function templates(): EntityCollection
    {
        $response = $this->client()->get('/form-templates');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Update a form submission.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateSubmission(string $id, array $data): FormSubmission
    {
        $response = $this->client()->patch("/form-submissions/{$id}", $data);

        $this->handleError($response);

        /** @var FormSubmission */
        return $this->mapToEntity($response->json('data', $response->json()));
    }
}
