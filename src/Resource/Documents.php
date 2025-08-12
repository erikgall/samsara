<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Documents\GetDocument;
use ErikGall\Samsara\Requests\Documents\GetDocuments;
use ErikGall\Samsara\Requests\Documents\PostDocument;
use ErikGall\Samsara\Requests\Documents\DeleteDocument;
use ErikGall\Samsara\Requests\Documents\GetDocumentPdf;
use ErikGall\Samsara\Requests\Documents\GetDocumentTypes;
use ErikGall\Samsara\Requests\Documents\GenerateDocumentPdf;

class Documents extends Resource
{
    /**
     * Create a new Document resource.
     *
     * @param  array  $payload  The data to create the document.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new PostDocument($payload));
    }

    /**
     * Delete a Document resource.
     *
     * @param  string  $id  ID of the document to delete.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteDocument($id));
    }

    /**
     * Find a Document resource by ID.
     *
     * @param  string  $id  ID of the document.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetDocument($id));
    }

    /**
     * Generate a PDF for a document.
     *
     * @param  array  $payload  The data to generate the PDF.
     * @return Response
     */
    public function generatePdf(array $payload = []): Response
    {
        return $this->connector->send(new GenerateDocumentPdf($payload));
    }

    /**
     * Get a list of Document resources.
     *
     * @param  string  $startTime  A start time in RFC 3339 format.
     * @param  string  $endTime  An end time in RFC 3339 format.
     * @param  string|null  $documentTypeId  ID of the document template type.
     * @param  string|null  $queryBy  Query by document creation time (`created`) or updated time (`updated`).
     * @return Response
     */
    public function get(
        string $startTime,
        string $endTime,
        ?string $documentTypeId = null,
        ?string $queryBy = null
    ): Response {
        return $this->connector->send(
            new GetDocuments($startTime, $endTime, $documentTypeId, $queryBy)
        );
    }

    /**
     * Get a PDF for a document by ID.
     *
     * @param  string  $id  ID of the PDF.
     * @return Response
     */
    public function getPdf(string $id): Response
    {
        return $this->connector->send(new GetDocumentPdf($id));
    }

    /**
     * Get available document types.
     *
     * @return Response
     */
    public function getTypes(): Response
    {
        return $this->connector->send(new GetDocumentTypes);
    }
}
