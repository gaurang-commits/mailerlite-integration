<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    /**
     * The subscriber response mock json string
     *
     * @var string
     */
    private $subscriberResponse;

    /**
     * The subscribers list mock json string
     *
     * @var string
     */
    private $subscriberListResponse;


    /**
     * Set up the test environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        //Fetch mocked response json
        $this->subscriberResponse = file_get_contents(base_path('tests/Data/CreateSubscriberSuccess.json'));
        //Fetch mocked response json
        $this->subscriberListResponse = file_get_contents(base_path('tests/Data/SubscribersSuccess.json'));
    }

    /**
     * Cleaning test environment
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function it_gets_all_subscribers()
    {
        //Faking the requests to prevent multiple API calls
        Http::fake([
            //Faking the count API
            'https://connect.mailerlite.com/api/subscribers?limit=0' =>
            Http::response('{
                "total": 100
            }
            ', 200),
            //Faking the subscribers list api
            'https://connect.mailerlite.com/api/subscribers' =>
            Http::response($this->subscriberListResponse, 200)
        ]);

        //prepare request parameters
        $request = [
            'length' => 10,
            'draw' => 1
        ];

        //Executing the api
        $response = $this->json('GET', '/api/subscribers', $request);

        //Assertions
        $response->assertOk();
        $response->assertJsonFragment([
            'isSuccess' => true
        ]);

        return $response->json()['data']['data'][0];
    }

    /** @test */
    public function it_throws_an_error_for_invalid_api_key()
    {
        Http::fake([
            //Faking Unauthenticated response
            'https://connect.mailerlite.com/api/*' =>
            Http::response('{
                "message": "Unauthenticated."
              }', 401)
        ]);

        //prepare request parameters
        $request = [
            'length' => 10,
            'draw' => 1
        ];
        //Executing the api
        $response = $this->json('GET', '/api/subscribers', $request);
        //Assertions
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'isSuccess' => false
        ]);
    }

    /** 
     * @test
     * 
     * @depends it_gets_all_subscribers
     */
    public function it_finds_a_subscriber($subscriber)
    {
        Http::fake([
            //Faking find subscriber response
            'https://connect.mailerlite.com/api/subscribers' =>
            Http::response($this->subscriberResponse, 200),
        ]);

        //prepare request parameters
        $request = [
            'search' => [
                'value' => $subscriber['email']
            ],
            'draw' => 1
        ];
        //Executing the api
        $response = $this->json('GET', '/api/subscribers', $request);

        //Assertions
        $response->assertOk();
        $response->assertJsonFragment([
            'isSuccess' => true
        ]);
    }

    /** 
     * @test
     * 
     */
    public function it_throws_error_when_subscriber_does_not_exists()
    {
        Http::fake([
            //Faking subscriber does not exist response
            'https://connect.mailerlite.com/api/subscribers' =>
            Http::response('{
                "message": "Resource not found."
            }', 404),
        ]);

        //prepare request parameters
        $request = [
            'search' => [
                'value' => 'random@email.com'
            ],
            'draw' => 1
        ];
        //Executing the api
        $response = $this->json('GET', '/api/subscribers', $request);

        //Assertions
        $response->assertNotFound();
        $response->assertJsonFragment([
            'isSuccess' => false
        ]);
    }

    /** 
     * @test
     */
    public function it_creates_a_subscriber()
    {
        Http::fake([
            //Faking create subscriber response
            'https://connect.mailerlite.com/api/subscribers' =>
            Http::response($this->subscriberResponse, 200),
        ]);

        //prepare request parameters
        $request = [
            'name' => "test",
            'country' => "India",
            'email' => 'foo@bar.com'
        ];
        //Executing the api
        $response = $this->json('POST', '/api/subscribers', $request);

        //Assertions
        $response->assertOk();
        $response->assertJsonFragment([
            'isSuccess' => true
        ]);
        return $response->json()['data'];
    }

    /** 
     * @test
     * @depends it_creates_a_subscriber
     */
    public function it_throws_error_when_creating_user_with_same_email($existingUser)
    {
        Http::fake([
            //Faking user exists response
            "https://connect.mailerlite.com/api/subscribers/{$existingUser['email']}" =>
            Http::response($this->subscriberResponse, 200),
        ]);

        //prepare request parameters
        $request = [
            'name' => "test",
            'country' => "India",
            'email' => $existingUser['email']
        ];

        //Executing the api
        $response = $this->json('POST', '/api/subscribers', $request);

        //Assertions
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'isSuccess' => false
        ]);
    }
}
