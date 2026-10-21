<?php

namespace Samsara\Resources\Concerns;

use Samsara\Data\Entity;
use Samsara\Resources\Resource;
use Samsara\Exceptions\ServerException;
use Samsara\Exceptions\SamsaraException;
use Samsara\Exceptions\NotFoundException;
use Samsara\Exceptions\RateLimitException;
use Samsara\Exceptions\ValidationException;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Exceptions\AuthenticationException;

/**
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @template TEntity of Entity
 *
 * @phpstan-require-extends Resource<TEntity>
 */
trait DeletesEntities
{
    /**
     * Delete an entity.
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ServerException
     * @throws SamsaraException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function delete(string $id): bool
    {
        $endpoint = $this->endpoint.'/'.$this->encodePathSegment($id);

        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($endpoint),
            $endpoint
        );

        return $response->successful();
    }
}
