<?php

namespace Actengage\CaseyJones\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

trait InteractsWithResponses
{
    /**
     * Instaniate a paginator of models given an HTTP response.
     *
     * @param string $class
     * @param array $model
     * @param array|null $options
     * @return LengthAwarePaginator
     */
    protected function paginate(ResponseInterface $response, array $options = []): LengthAwarePaginator
    {
        $body = $this->decode($response);
        
        return new LengthAwarePaginator(
            items: Arr::get($body, 'data'),
            total: Arr::get($body, 'total'),
            perPage: Arr::get($body, 'per_page'),
            currentPage: Arr::get($body, 'current_page'),
            options: $options
        );
    }

    /**
     * Decode the body of a response.
     *
     * @param ResponseInterface $response
     * @return array<string,mixed>
     */
    protected function decode(ResponseInterface $response): array
    {
        return json_decode($response->getBody(), true);
    }
}