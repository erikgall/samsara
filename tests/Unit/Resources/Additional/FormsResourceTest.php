<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Form\FormTemplate;
use Samsara\Data\Form\FormSubmission;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\FormsResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
                    'status' => 'notStarted',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $submission = $resource->createSubmission([
            'formTemplate' => ['id' => 'template-1'],
            'status'       => 'notStarted',
        ]);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('submission-123', $submission->id);
    }

    #[Test]
    public function it_can_export_pdf(): void
    {
        $this->http->fake([
            '*/form-submissions/pdf-exports*' => $this->http->response([
                'data' => [
                    'id'        => 'export-123',
                    'pdfId'     => 'pdf-123',
                    'jobStatus' => 'pending',
                ],
            ], 202),
        ]);

        $resource = new FormsResource($this->samsara);

        $result = $resource->exportPdf('submission-1');

        $this->assertIsArray($result);
        $this->assertSame('export-123', $result['id']);
    }

    #[Test]
    public function it_can_get_pdf_export(): void
    {
        $this->http->fake([
            '*/form-submissions/pdf-exports*' => $this->http->response([
                'data' => [
                    'id'        => 'export-123',
                    'pdfId'     => 'pdf-123',
                    'jobStatus' => 'completed',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $result = $resource->getPdfExport('export-123');

        $this->assertIsArray($result);
        $this->assertSame('completed', $result['jobStatus']);
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
                    ['id' => 'template-1', 'title' => 'Template 1'],
                    ['id' => 'template-2', 'title' => 'Template 2'],
                ],
                'pagination' => ['hasNextPage' => false],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $templates = $resource->templates()->get();

        $this->assertInstanceOf(FormTemplate::class, $templates->first());
        $this->assertCount(2, $templates);
    }

    #[Test]
    public function it_can_update_submission(): void
    {
        $this->http->fake([
            '*/form-submissions' => $this->http->response([
                'data' => [
                    'id'     => 'submission-123',
                    'status' => 'approved',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $submission = $resource->updateSubmission('submission-123', ['status' => 'approved']);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('approved', $submission->status);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new FormsResource($this->samsara);

        $this->assertSame('/form-submissions', $resource->getEndpoint());
    }
}
