<?php

namespace ErikGall\Samsara\Requests\Coaching;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * putDriverCoachAssignment.
 *
 * This endpoint will update an existing or create a new coach-to-driver assignment for your
 * organization based on the parameters passed in. This endpoint should only be used for existing Coach
 * to Driver assignments. In order to remove a driver-coach-assignment for a given driver, set coachId
 * to null
 *
 *  <b>Rate limit:</b> 10 requests/sec (learn more about rate limits <a
 * href="https://developers.samsara.com/docs/rate-limits" target="_blank">here</a>).
 *
 * To use this
 * endpoint, select **Write Coaching** under the Coaching category when creating or editing an API
 * token. <a href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class PutDriverCoachAssignment extends Request
{
    protected Method $method = Method::PUT;

    /**
     * @param  string  $driverId  Required string ID of the driver. This is a unique Samsara ID of a driver.
     * @param  string|null  $coachId  Optional string ID of the coach. This is a unique Samsara user ID. If not provided, existing coach assignment will be removed.
     */
    public function __construct(protected string $driverId, protected ?string $coachId = null, protected array $payload = []) {}

    public function defaultQuery(): array
    {
        return array_filter(['driverId' => $this->driverId, 'coachId' => $this->coachId]);
    }

    public function resolveEndpoint(): string
    {
        return '/coaching/driver-coach-assignments';
    }

    /**
     * Default body.
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
