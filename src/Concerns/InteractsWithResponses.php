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
     * @param  array<string, mixed>  $options
     * @return LengthAwarePaginator<int, array<array-key, mixed>>
     */
    protected function paginate(ResponseInterface $response, array $options = []): LengthAwarePaginator
    {
        $body = $this->decode($response);

        $data = Arr::get($body, 'data');
        $total = Arr::get($body, 'total');
        $perPage = Arr::get($body, 'per_page');
        $currentPage = Arr::get($body, 'current_page');

        $items = [];

        if (is_iterable($data)) {
            foreach ($data as $item) {
                if (is_array($item)) {
                    $items[] = $item;
                }
            }
        }

        return new LengthAwarePaginator(
            items: $items,
            total: is_int($total) ? $total : 0,
            perPage: is_int($perPage) ? $perPage : 0,
            currentPage: is_int($currentPage) ? $currentPage : null,
            options: $options
        );
    }

    /**
     * Decode the body of a response.
     *
     * @return array<array-key, mixed>
     */
    protected function decode(ResponseInterface $response): array
    {
        $decoded = json_decode((string) $response->getBody(), true);

        return is_array($decoded) ? $decoded : [];
    }
}
