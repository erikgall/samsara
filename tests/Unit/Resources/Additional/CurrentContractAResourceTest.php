<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Throwable;
use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Samsara\Data\Form\FormTemplate;
use Samsara\Data\Alert\AlertIncident;
use Samsara\Data\Form\FormSubmission;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\WorkOrder;
use Samsara\Data\Maintenance\ServiceTask;
use Samsara\Data\Alert\AlertConfiguration;
use Samsara\Exceptions\ValidationException;
use Samsara\Resources\Additional\HubsResource;
use Samsara\Resources\Additional\FormsResource;
use Samsara\Resources\Additional\AlertsResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SettingsResource;
use Samsara\Resources\Additional\WorkOrdersResource;
use Samsara\Resources\Additional\CameraMediaResource;
use Samsara\Resources\Additional\RouteEventsResource;

/**
 * Current first-party contract tests for the first Additional resource group.
 */
class CurrentContractAResourceTest extends TestCase
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
    public function alert_builders_use_current_paths_and_endpoint_specific_entities(): void
    {
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'config-1', 'isEnabled' => true]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new AlertsResource($this->samsara);

        $configurations = $resource->configurations()
            ->where('ids', ['config-1', 'config-2'])
            ->where('status', 'enabled')
            ->get();
        $this->assertInstanceOf(AlertConfiguration::class, $configurations->first());
        $this->assertRequest('GET', '/alerts/configurations', null);
        $this->assertRawQueryPairs([
            ['ids', 'config-1'],
            ['ids', 'config-2'],
            ['status', 'enabled'],
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'configurationId' => 'config-1',
                    'isResolved'      => false,
                    'happenedAtTime'  => '2026-08-01T00:00:00Z',
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $incidents = $resource->incidents()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->where('configurationIds', ['config-1', 'config-2'])
            ->get();
        $this->assertInstanceOf(AlertIncident::class, $incidents->first());
        $this->assertRequest('GET', '/alerts/incidents/stream', null);
        $this->assertRawQueryPairs([
            ['startTime', '2026-08-01T00:00:00Z'],
            ['configurationIds', 'config-1'],
            ['configurationIds', 'config-2'],
        ]);
    }

    #[Test]
    public function alert_configuration_methods_follow_the_collection_contract(): void
    {
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['data' => ['id' => 'config-1', 'isEnabled' => true]]),
        ]);
        $resource = new AlertsResource($this->samsara);

        $created = $resource->createConfiguration([
            'actions'   => [],
            'isEnabled' => true,
            'name'      => 'Harsh event',
            'scope'     => ['all' => true],
            'triggers'  => [],
        ]);
        $this->assertInstanceOf(AlertConfiguration::class, $created);
        $this->assertRequest('POST', '/alerts/configurations', [
            'actions'   => [],
            'isEnabled' => true,
            'name'      => 'Harsh event',
            'scope'     => ['all' => true],
            'triggers'  => [],
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['data' => ['id' => 'config-1', 'isEnabled' => false]]),
        ]);
        $updated = $resource->updateConfiguration('config-1', ['isEnabled' => false]);
        $this->assertInstanceOf(AlertConfiguration::class, $updated);
        $this->assertRequest('PATCH', '/alerts/configurations', [
            'isEnabled' => false,
            'id'        => 'config-1',
        ]);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response([], 204)]);
        $this->assertTrue($resource->deleteConfigurations('config-1'));
        $this->assertQueryRequest('DELETE', '/alerts/configurations', ['id' => 'config-1']);
    }

    #[Test]
    public function camera_media_methods_preserve_the_current_response_wrappers(): void
    {
        $parameters = [
            'vehicleIds'     => 'vehicle-1',
            'startTime'      => '2026-08-01T00:00:00Z',
            'endTime'        => '2026-08-01T01:00:00Z',
            'inputs'         => ['dashcamRoadFacing', 'dashcamDriverFacing'],
            'mediaTypes'     => ['image', 'videoHighRes'],
            'triggerReasons' => ['harshAccel', 'harshBrake'],
        ];
        $envelope = [
            'data'       => ['media' => [['id' => 'media-1']]],
            'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
        ];
        $this->http->fake(['*' => $this->http->response($envelope)]);
        $resource = new CameraMediaResource($this->samsara);

        $result = $resource->get($parameters);

        $this->assertSame($envelope, $result);
        $this->assertRequest('GET', '/cameras/media', null);
        $this->assertRawQueryPairs([
            ['vehicleIds', 'vehicle-1'],
            ['startTime', '2026-08-01T00:00:00Z'],
            ['endTime', '2026-08-01T01:00:00Z'],
            ['inputs', 'dashcamRoadFacing'],
            ['inputs', 'dashcamDriverFacing'],
            ['mediaTypes', 'image'],
            ['mediaTypes', 'videoHighRes'],
            ['triggerReasons', 'harshAccel'],
            ['triggerReasons', 'harshBrake'],
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['data' => ['media' => [['id' => 'media-1']]]]),
        ]);
        $retrieval = $resource->getRetrieval('retrieval-1');
        $this->assertIsArray($retrieval);
        $this->assertRequest('GET', '/cameras/media/retrieval', ['retrievalId' => 'retrieval-1']);

        $payload = [
            'vehicleId' => 'vehicle-1',
            'startTime' => '2026-08-01T00:00:00Z',
            'endTime'   => '2026-08-01T01:00:00Z',
            'mediaType' => 'videoHighRes',
        ];
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['data' => ['retrievalId' => 'retrieval-1', 'quotaStatus' => 'available']]),
        ]);
        $created = $resource->retrieve($payload);
        $this->assertSame('retrieval-1', $created['retrievalId']);
        $this->assertRequest('POST', '/cameras/media/retrieval', $payload);
    }

    #[Test]
    public function cursor_builders_without_an_official_limit_omit_the_default_page_size(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $alerts = new AlertsResource($this->samsara);
        $forms = new FormsResource($this->samsara);
        $workOrders = new WorkOrdersResource($this->samsara);
        $builders = [
            $alerts->configurations(),
            $alerts->incidents()
                ->where('startTime', '2026-08-01T00:00:00Z')
                ->where('configurationIds', ['config-1']),
            $forms->submissionsStream()->where('startTime', '2026-08-01T00:00:00Z'),
            $forms->templates(),
            $workOrders->query(),
            $workOrders->serviceTasks(),
            $workOrders->stream()->where('startTime', '2026-08-01T00:00:00Z'),
        ];

        foreach ($builders as $builder) {
            $builder->getWithPagination();
        }

        $this->http->assertNotSent(fn (Request $request): bool => array_key_exists('limit', $request->data()));
        $this->http->assertSentCount(count($builders));
    }

    #[Test]
    public function direct_and_builder_failures_use_the_sdk_error_hierarchy(): void
    {
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['message' => 'Invalid settings', 'requestId' => 'req-1'], 400),
        ]);

        try {
            (new SettingsResource($this->samsara))->compliance();
            $this->fail('Expected the direct request to fail.');
        } catch (Throwable $exception) {
            $this->assertInstanceOf(ValidationException::class, $exception);
            if ($exception instanceof ValidationException) {
                $this->assertSame(400, $exception->getCode());
                $this->assertSame('/fleet/settings/compliance', $exception->getContext()['endpoint']);
            }
        }

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response(['message' => 'Invalid alert query', 'requestId' => 'req-2'], 400),
        ]);

        $this->expectException(ValidationException::class);
        (new AlertsResource($this->samsara))->configurations()->get();
    }

    #[Test]
    public function form_builders_map_submissions_and_templates_to_their_own_entities(): void
    {
        $resource = new FormsResource($this->samsara);
        $this->http->fake(['*' => $this->http->response(['data' => [['id' => 'submission-1']]])]);

        $submissions = $resource->submissions()->where('ids', 'submission-1')->get();

        $this->assertInstanceOf(FormSubmission::class, $submissions->first());
        $this->assertRequest('GET', '/form-submissions', ['ids' => 'submission-1']);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'submission-2']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $stream = $resource->submissionsStream()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->get();
        $this->assertInstanceOf(FormSubmission::class, $stream->first());
        $this->assertRequest('GET', '/form-submissions/stream', [
            'startTime' => '2026-08-01T00:00:00Z',
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'template-1', 'title' => 'Inspection']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $templateQuery = $resource->templates();
        $this->assertInstanceOf(Builder::class, $templateQuery);
        $templates = $templateQuery->where('ids', 'template-1')->get();
        $this->assertInstanceOf(FormTemplate::class, $templates->first());
        $this->assertRequest('GET', '/form-templates', ['ids' => 'template-1']);
    }

    #[Test]
    public function form_submission_writes_use_collection_endpoints_and_current_parameters(): void
    {
        $resource = new FormsResource($this->samsara);
        $payload = [
            'formTemplate' => ['id' => 'template-1'],
            'status'       => 'notStarted',
        ];
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'submission-1']])]);

        $created = $resource->createSubmission($payload);

        $this->assertInstanceOf(FormSubmission::class, $created);
        $this->assertRequest('POST', '/form-submissions', $payload);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'submission-1', 'status' => 'approved']])]);
        $updated = $resource->updateSubmission('submission-1', ['status' => 'approved']);
        $this->assertInstanceOf(FormSubmission::class, $updated);
        $this->assertRequest('PATCH', '/form-submissions', [
            'status' => 'approved',
            'id'     => 'submission-1',
        ]);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['pdfId' => 'pdf-1']], 202)]);
        $this->assertIsArray($resource->exportPdf('submission-1'));
        $this->assertQueryRequest('POST', '/form-submissions/pdf-exports', ['id' => 'submission-1']);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['pdfId' => 'pdf-1']])]);
        $this->assertIsArray($resource->getPdfExport('pdf-1'));
        $this->assertRequest('GET', '/form-submissions/pdf-exports', ['pdfId' => 'pdf-1']);
    }

    #[Test]
    public function hubs_and_route_events_execute_their_current_stream_contracts(): void
    {
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'hub-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $hubs = (new HubsResource($this->samsara))->query()->where('hubIds', 'hub-1')->get();
        $this->assertSame(Entity::class, $hubs->first()::class);
        $this->assertRequest('GET', '/hubs', ['hubIds' => 'hub-1']);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'route-event-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $routeEvents = (new RouteEventsResource($this->samsara))
            ->query()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->get();
        $this->assertSame(Entity::class, $routeEvents->first()::class);
        $this->assertRequest('GET', '/route-events/stream', [
            'startTime' => '2026-08-01T00:00:00Z',
        ]);
    }

    #[Test]
    public function settings_methods_use_the_fleet_namespace_and_preserve_documented_shapes(): void
    {
        $resource = new SettingsResource($this->samsara);

        $this->http->fake(['*' => $this->http->response(['data' => ['canadaHosEnabled' => true]])]);
        $this->assertIsArray($resource->compliance());
        $this->assertRequest('GET', '/fleet/settings/compliance');

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['canadaHosEnabled' => false]])]);
        $this->assertIsArray($resource->updateCompliance(['canadaHosEnabled' => false]));
        $this->assertRequest('PATCH', '/fleet/settings/compliance', ['canadaHosEnabled' => false]);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['driverFleetId' => 'fleet-1']])]);
        $this->assertIsArray($resource->driverApp());
        $this->assertRequest('GET', '/fleet/settings/driver-app');

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['orgVehicleSearch' => true]])]);
        $this->assertIsArray($resource->updateDriverApp(['orgVehicleSearch' => true]));
        $this->assertRequest('PATCH', '/fleet/settings/driver-app', ['orgVehicleSearch' => true]);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => [['eventType' => 'harshBrake']]])]);
        $safety = $resource->safety();
        $this->assertIsArray($safety);
        $this->assertSame('harshBrake', $safety[0]['eventType']);
        $this->assertRequest('GET', '/fleet/settings/safety');
    }

    #[Test]
    public function work_order_methods_use_endpoint_specific_response_types(): void
    {
        $resource = new WorkOrdersResource($this->samsara);
        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'work-order-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $workOrders = $resource->query()
            ->where('ids', ['work-order-1', 'work-order-2'])
            ->where('includeExternalIds', true)
            ->get();
        $this->assertInstanceOf(WorkOrder::class, $workOrders->first());
        $this->assertRequest('GET', '/maintenance/work-orders', null);
        $this->assertRawQueryPairs([
            ['ids', 'work-order-1'],
            ['ids', 'work-order-2'],
            ['includeExternalIds', '1'],
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'service-task-1', 'name' => 'Oil change']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $serviceTaskQuery = $resource->serviceTasks();
        $this->assertInstanceOf(Builder::class, $serviceTaskQuery);
        $serviceTasks = $serviceTaskQuery
            ->where('ids', ['service-task-1', 'service-task-2'])
            ->where('includeArchived', false)
            ->get();
        $this->assertInstanceOf(ServiceTask::class, $serviceTasks->first());
        $this->assertRequest('GET', '/maintenance/service-tasks', null);
        $this->assertRawQueryPairs([
            ['ids', 'service-task-1'],
            ['ids', 'service-task-2'],
            ['includeArchived', '0'],
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'work-order-2']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $stream = $resource->stream()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->where('workOrderStatuses', ['open', 'completed'])
            ->where('assetIds', ['asset-1', 'asset-2'])
            ->where('assignedUserIds', ['user-1', 'user-2'])
            ->get();
        $this->assertInstanceOf(WorkOrder::class, $stream->first());
        $this->assertRequest('GET', '/maintenance/work-orders/stream', null);
        $this->assertRawQueryPairs([
            ['startTime', '2026-08-01T00:00:00Z'],
            ['workOrderStatuses', 'open'],
            ['workOrderStatuses', 'completed'],
            ['assetIds', 'asset-1'],
            ['assetIds', 'asset-2'],
            ['assignedUserIds', 'user-1'],
            ['assignedUserIds', 'user-2'],
        ]);

        $invoice = [
            'file'        => ['base64Content' => 'ZmlsZQ==', 'contentType' => 'application/pdf'],
            'workOrderId' => 'work-order-1',
        ];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['workOrderId' => 'work-order-1']])]);
        $result = $resource->uploadInvoiceScan($invoice);
        $this->assertSame('work-order-1', $result['workOrderId']);
        $this->assertRequest('POST', '/maintenance/invoice-scans', $invoice);
    }

    /**
     * Assert an exact query-only request.
     *
     * @param  array<string, string>  $query
     */
    private function assertQueryRequest(string $method, string $path, array $query): void
    {
        $this->http->assertSent(function (Request $request) use ($method, $path, $query): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $actualQuery);

            return $request->method() === $method
                && parse_url($request->url(), PHP_URL_PATH) === $path
                && $actualQuery === $query
                && $request->data() === [];
        });
    }

    /**
     * Assert raw query pairs without losing repeated keys through parse_str().
     *
     * @param  array<int, array{string, string}>  $expected
     */
    private function assertRawQueryPairs(array $expected): void
    {
        $this->http->assertSent(function (Request $request) use ($expected): bool {
            $query = (string) parse_url($request->url(), PHP_URL_QUERY);
            $actual = array_map(function (string $pair): array {
                [$key, $value] = array_pad(explode('=', $pair, 2), 2, '');

                return [rawurldecode($key), rawurldecode($value)];
            }, $query === '' ? [] : explode('&', $query));

            sort($actual);
            sort($expected);

            return $actual === $expected;
        });
    }

    /**
     * Assert the exact HTTP method, path, and body/query data.
     *
     * @param  array<string, mixed>|null  $data
     */
    private function assertRequest(string $method, string $path, ?array $data = []): void
    {
        $this->http->assertSent(fn (Request $request): bool => $request->method() === $method
            && parse_url($request->url(), PHP_URL_PATH) === $path
            && ($data === null || $request->data() == $data));
    }

    /**
     * Reset the fake factory so each grouped assertion receives its own fixture.
     */
    private function resetHttp(): void
    {
        $this->http = new HttpFactory;
        $this->samsara->setHttpFactory($this->http);
    }
}
