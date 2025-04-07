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
     * @param  string  $id  ID of the document to delete
     */
    public function deleteDocument(string $id): Response
    {
        return $this->connector->send(new DeleteDocument($id));
    }

    public function generateDocumentPdf(): Response
    {
        return $this->connector->send(new GenerateDocumentPdf);
    }

    /**
     * @param  string  $id  ID of the document
     */
    public function getDocument(string $id): Response
    {
        return $this->connector->send(new GetDocument($id));
    }

    /**
     * @param  string  $id  ID of the pdf.
     */
    public function getDocumentPdf(string $id): Response
    {
        return $this->connector->send(new GetDocumentPdf($id));
    }

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string  $documentTypeId  ID of the document template type.
     * @param  string  $queryBy  Query by document creation time (`created`) or updated time (`updated`). Defaults to `created`.
     */
    public function getDocuments(string $startTime, string $endTime, ?string $documentTypeId = null, ?string $queryBy = null): Response
    {
        return $this->connector->send(new GetDocuments($startTime, $endTime, $documentTypeId, $queryBy));
    }

    public function getDocumentTypes(): Response
    {
        return $this->connector->send(new GetDocumentTypes);
    }

    public function postDocument(): Response
    {
        return $this->connector->send(new PostDocument);
    }
}
