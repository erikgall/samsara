<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrainingAssignmentsStream.
 *
 * Returns all training assignments data that has been created or modified for your organization based
 * on the time parameters passed in. Results are paginated and are sorted by last modified date. If you
 * include an endTime, the endpoint will return data up until that point (exclusive). If you don't
 * include an endTime, you can continue to poll the API real-time with the pagination cursor that gets
 * returned on every call.
 *
 * **Beta:** This endpoint is in beta and is likely to change before being
 * broadly available. Reach out to your Samsara Representative to have Training APIs enabled for your
 * organization.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Read Training Assignments** under the Training Assignments category when creating
 * or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetTrainingAssignmentsStream extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $startTime  A start time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  string|null  $endTime  An end time in RFC 3339 format. Defaults to now if not provided. Millisecond precision and timezones are supported. (Examples: 2019-06-13T19:08:25Z, 2019-06-13T19:08:25.455Z, OR 2015-09-15T14:00:12-04:00).
     * @param  array|null  $learnerIds  Optional string of comma separated learner IDs. If learner ID is present, training assignments for the specified learner(s) will be returned. Max value for this value is 100 objects. Example: `learnerIds=driver-281474,driver-46282156`
     * @param  array|null  $courseIds  Optional string of comma separated course IDs. If course ID is present, training assignments for the specified course ID(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses. Example: `courseIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array|null  $status  Optional string of comma separated values. If status is present, training assignments for the specified status(s) will be returned. Valid values: "notStarted", "inProgress", "completed". Defaults to returning all courses.
     */
    public function __construct(
        protected string $startTime,
        protected ?string $endTime = null,
        protected ?array $learnerIds = null,
        protected ?array $courseIds = null,
        protected ?array $status = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'startTime'  => $this->startTime,
            'endTime'    => $this->endTime,
            'learnerIds' => $this->learnerIds,
            'courseIds'  => $this->courseIds,
            'status'     => $this->status,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/training-assignments/stream';
    }
}
