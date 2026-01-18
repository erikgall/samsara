<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Data\Form\FormSubmission;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\FormsResource;

/**
 * Unit tests for the FormsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FormsResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_submission(): void
    {
        $this->http->fake([
            '*/form-submissions' => $this->http->response([
                'data' => [
                    'id'     => 'submission-123',
                    'status' => 'draft',
                ],
            ], 201),
        ]);

        $resource = new FormsResource($this->samsara);

        $submission = $resource->createSubmission([
            'formTemplateId' => 'template-1',
        ]);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('submission-123', $submission->id);
    }

    #[Test]
    public function it_can_export_pdf(): void
    {
        $this->http->fake([
            '*/form-submissions/pdf-exports' => $this->http->response([
                'data' => [
                    'id'     => 'export-123',
                    'status' => 'pending',
                ],
            ], 201),
        ]);

        $resource = new FormsResource($this->samsara);

        $result = $resource->exportPdf(['submissionIds' => ['submission-1']]);

        $this->assertIsArray($result);
        $this->assertSame('export-123', $result['id']);
    }

    #[Test]
    public function it_can_get_pdf_export(): void
    {
        $this->http->fake([
            '*/form-submissions/pdf-exports/export-123' => $this->http->response([
                'data' => [
                    'id'     => 'export-123',
                    'status' => 'completed',
                    'url'    => 'https://example.com/pdf',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $result = $resource->getPdfExport('export-123');

        $this->assertIsArray($result);
        $this->assertSame('completed', $result['status']);
    }

    #[Test]
    public function it_can_get_submissions_builder(): void
    {
        $resource = new FormsResource($this->samsara);

        $builder = $resource->submissions();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_submissions_stream_builder(): void
    {
        $resource = new FormsResource($this->samsara);

        $builder = $resource->submissionsStream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_templates(): void
    {
        $this->http->fake([
            '*/form-templates' => $this->http->response([
                'data' => [
                    ['id' => 'template-1', 'name' => 'Template 1'],
                    ['id' => 'template-2', 'name' => 'Template 2'],
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $templates = $resource->templates();

        $this->assertInstanceOf(EntityCollection::class, $templates);
        $this->assertCount(2, $templates);
    }

    #[Test]
    public function it_can_update_submission(): void
    {
        $this->http->fake([
            '*/form-submissions/submission-123' => $this->http->response([
                'data' => [
                    'id'     => 'submission-123',
                    'status' => 'submitted',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $submission = $resource->updateSubmission('submission-123', ['status' => 'submitted']);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('submitted', $submission->status);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new FormsResource($this->samsara);

        $this->assertSame('/form-submissions', $resource->getEndpoint());
    }
}
