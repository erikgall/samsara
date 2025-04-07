<?php

namespace ErikGall\Samsara\Requests\BetaApis;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * getTrainingCourses.
 *
 * Returns all training courses data. Results are paginated.
 *  Courses in the ‘draft’ status are
 * excluded from the data returned by this endpoint.
 *
 *  <b>Rate limit:</b> 5 requests/sec (learn more
 * about rate limits <a href="https://developers.samsara.com/docs/rate-limits"
 * target="_blank">here</a>).
 *
 * To use this endpoint, select **Read Training Courses** under the
 * Training Courses category when creating or editing an API token. <a
 * href="https://developers.samsara.com/docs/authentication#scopes-for-api-tokens"
 * target="_blank">Learn More.</a>
 *
 *
 *  **Submit Feedback**: Likes, dislikes, and API feature requests
 * should be filed as feedback in our <a href="https://forms.gle/zkD4NCH7HjKb7mm69" target="_blank">API
 * feedback form</a>. If you encountered an issue or noticed inaccuracies in the API documentation,
 * please <a href="https://www.samsara.com/help" target="_blank">submit a case</a> to our support team.
 */
class GetTrainingCourses extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array|null  $courseIds  Optional string of comma separated course IDs. If course ID is present, training assignments for the specified course ID(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses. Example: `courseIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array|null  $categoryIds  Optional string of comma separated course category IDs. If courseCategoryId is present, training courses for the specified course category(s) will be returned. Max value for this value is 100 objects. Defaults to returning all courses.  Example: `categoryIds=a4db8702-79d5-4396-a717-e301d52ecc11,c6490f6a-d84e-49b5-b0ad-b6baae304075`
     * @param  array|null  $status  Optional string of comma separated values. If status is present, training courses with the specified status(s) will be returned. Valid values: “published”, “deleted”, “archived”. Defaults to returning all courses.
     */
    public function __construct(
        protected ?array $courseIds = null,
        protected ?array $categoryIds = null,
        protected ?array $status = null
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'courseIds'   => $this->courseIds,
            'categoryIds' => $this->categoryIds,
            'status'      => $this->status,
        ]);
    }

    public function resolveEndpoint(): string
    {
        return '/training-courses';
    }
}
