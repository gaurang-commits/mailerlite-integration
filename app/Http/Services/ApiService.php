<?php

namespace App\Http\Services;

use App\Models\ApiKey;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Throwable;

class ApiService
{
    /**
     * Api response status code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Api response data
     *
     * @var int
     */
    protected $data;

    /**
     * Api client
     *
     * @var  Illuminate\Support\Facades\Http
     */
    protected $client;

    /**
     * Api key
     *
     * @var string
     */
    private $apiKey;

    /**
     * Create a new Api Service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setApiKey();

        $this->client = Http::withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-MailerLite-ApiKey' => $this->apiKey
        ])->baseUrl(config('services.mailerlite.endpoint'));
    }

    /**
     * Get api status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * Set api status code
     *
     * @return App\Http\Services\ApiService
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode =  $statusCode;

        return $this;
    }

    /**
     * Return Api response
     *
     * @param array $data
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function respond($data = [])
    {
        $message = trans('mailerlite.api_response.success');
        if ($this->getStatusCode() >= 300) {
            $message = isset($data['message']) ? $data['message'] : trans('mailerlite.api_response.failure');
            $data = [];
        }

        $response = [
            'isSuccess' => ($this->getStatusCode() < 300) ? true : false,
            'message' => $message,
            'data' => $data
        ];

        return Response::json($response, $this->getStatusCode());
    }

    /**
     * Set api key

     * @return void
     */
    protected function setApiKey()
    {
        $this->apiKey = optional(ApiKey::where('status', 1)->first())->key;
        if (!$this->apiKey) {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-MailerLite-ApiKey' => config('services.mailerlite.key')
            ])->get(config('services.mailerlite.validation_endpoint'));
            if ($response->successful()) {
                $this->apiKey = ApiKey::create([
                    'key' => config('services.mailerlite.key'),
                    'status' => 1
                ])->key;
            } else {
                throw new Exception('Api ket is invalid, or not available!');
            }
        }
    }
}
