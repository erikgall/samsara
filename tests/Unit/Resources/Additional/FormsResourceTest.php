<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
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
        $this->http->preventStrayRequests();
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

        $payload = [
            'formTemplate' => ['id' => 'template-1'],
            'status'       => 'notStarted',
        ];
        $submission = $resource->createSubmission($payload);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('submission-123', $submission->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/form-submissions'
            && $request->data() === $payload);
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
        $this->assertSame('pdf-123', $result['pdfId']);
        $this->assertSame('pending', $result['jobStatus']);
    }

    #[Test]
    public function it_can_get_pdf_export(): void
    {
        $this->http->fake([
            '*/form-submissions/pdf-exports*' => $this->http->response([
                'data' => [
                    'id'        => 'submission-1',
                    'pdfId'     => 'pdf-123',
                    'jobStatus' => 'done',
                    'pdfUrl'    => 'https://example.test/form.pdf',
                ],
            ], 200),
        ]);

        $resource = new FormsResource($this->samsara);

        $result = $resource->getPdfExport('pdf-123');

        $this->assertIsArray($result);
        $this->assertSame('done', $result['jobStatus']);
        $this->assertSame('https://example.test/form.pdf', $result['pdfUrl']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/form-submissions/pdf-exports'
            && str_contains($request->url(), 'pdfId=pdf-123'));
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
