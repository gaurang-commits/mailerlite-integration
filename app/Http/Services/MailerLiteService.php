<?php

namespace App\Http\Services;

use App\Transformers\SubscriberTransformer;
use Illuminate\Http\JsonResponse;

class MailerLiteService extends ApiService
{
    /**
     * subscriber transformer object
     *
     * @var App\Transformers\SubscriberTransformer
     */
    protected $subscriberTransformer;

    /**
     * Create a new MailerLiteService instance.
     *
     * @return void
     */
    public function __construct(SubscriberTransformer $subscriberTransformer)
    {
        parent::__construct();

        $this->subscriberTransformer = $subscriberTransformer;
    }
    /**
     * Get all subscribers
     *
     * @param int $limit
     *
     * @param string $cursor
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function get(int $limit = 100, string $cursor = null): JsonResponse
    {
        $endpoint = "/subscribers?limit={$limit}";
        if ($cursor) {
            $parsedCursorUrl = parse_url($cursor)['query'];
            $endpoint = "/subscribers?{$parsedCursorUrl}&limit={$limit}";
        }
        $response = $this->client->get($endpoint);

        $responseData = $response->json();
        if ($response->successful() && $limit != 0) {
            $responseData = $this->subscriberTransformer->transformCollection($response->json());
        }

        return $this->setStatusCode($response->status())->respond($responseData);
    }


    /**
     * Create a subscriber
     *
     * @param array $subscriber
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function create(array $subscriber): JsonResponse
    {
        $subscriberExists = $this->find($subscriber['email']);
        if (!$subscriberExists->getData()->isSuccess) {
            $response = $this->client->post('/subscribers', $subscriber);
            if ($response->successful()) {
                return $this->setStatusCode($response->status())->respond(
                    $this->subscriberTransformer->transform($response->json()['data'])
                );
            }
            return $this->setStatusCode($response->status())->respond($response->json());
        }
        return $this->setStatusCode(400)->respond([
            'message' => trans('mailerlite.api_response.user_already_exists', ['email' => $subscriber['email']])
        ]);
    }

    /**
     * Delete a subscriber
     *
     * @param string $id
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        $response = $this->client->delete("/subscribers/$id");
        return $this->setStatusCode($response->status())->respond($response->json());
    }

    /**
     * Update a subscriber
     *
     * @param string $id
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(string $id, array $data): JsonResponse
    {
        $response = $this->client->put("/subscribers/$id", $data);
        $responseData = $response->json();
        if ($response->successful()) {
            $responseData = $this->subscriberTransformer->transform($response->json()['data']);
        }
        return $this->setStatusCode($response->status())->respond($responseData);
    }

    /**
     * Find a subscriber
     *
     * @param string $email
     * 
     * @return Illuminate\Http\JsonResponse;
     */
    public function find(string $email): JsonResponse
    {
        $response = $this->client->get("/subscribers/{$email}");
        $responseData = $response->json();

        if ($response->successful()) {
            $responseData = $this->subscriberTransformer->transform($response->json()['data']);
        }
        return $this->setStatusCode($response->status())->respond($responseData);
    }
}
