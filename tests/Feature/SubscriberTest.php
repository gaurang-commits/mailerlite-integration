<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use SebastianBergmann\Type\TrueType;
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
        $this->fakeApiValidation();
        //Faking the requests to prevent multiple API calls
        Http::fake([
            //Faking the count API
            config('services.mailerlite.endpoint') . 'subscribers?limit=0' =>
            Http::response('{
                "total": 100
            }
            ', 200),
            //Faking the subscribers list api
            config('services.mailerlite.endpoint') . '/subscribers' =>
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
        $this->fakeApiValidation();
        Http::fake([
            //Faking Unauthenticated response
            config('services.mailerlite.endpoint') . '*' =>
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
        $this->fakeApiValidation();
        Http::fake([
            //Faking find subscriber response
            config('services.mailerlite.endpoint') . 'subscribers' =>
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
        $this->fakeApiValidation();
        Http::fake([
            //Faking subscriber does not exist response
            config('services.mailerlite.endpoint') . 'subscribers' =>
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
        $this->fakeApiValidation();
        Http::fake([
            //Faking create subscriber response
            config('services.mailerlite.endpoint') . 'subscribers' =>
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
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/{$existingUser['email']}" =>
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

    /** 
     * @test
     * 
     * @depends it_creates_a_subscriber
     */
    public function it_updated_a_subscriber($existingUser)
    {
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/{$existingUser['email']}" =>
            Http::response($this->subscriberResponse, 200),
        ]);
        $request = [
            'name' => 'test',
            'country' => 'India'
        ];
        //Executing the api
        $response = $this->json('PUT', '/api/subscribers/' . $existingUser['email'], $request);

        //Assertions
        $response->assertOk();
        $response->assertJsonFragment([
            'isSuccess' => true
        ]);
    }

    /** 
     * @test
     * 
     * @depends it_creates_a_subscriber
     */
    public function it_throws_error_on_invalid_updated_request($existingUser)
    {
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/{$existingUser['email']}" =>
            Http::response($this->subscriberResponse, 200),
        ]);
        $request = [
            'country' => 'India'
        ];
        //Executing the api
        $response = $this->json('PUT', '/api/subscribers/' . $existingUser['email'], $request);

        //Assertions
        $response->assertStatus(422);
    }

    /** 
     * @test
     * 
     * @depends it_creates_a_subscriber
     */
    public function it_throws_error_on_invalid_country_on_update_request($existingUser)
    {
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/{$existingUser['email']}" =>
            Http::response($this->subscriberResponse, 200),
        ]);
        $request = [
            'name' => 'test',
            'country' => 'test'
        ];
        //Executing the api
        $response = $this->json('PUT', '/api/subscribers/' . $existingUser['email'], $request);

        //Assertions
        $response->assertStatus(422);
    }

    /** 
     * @test
     * 
     * @depends it_creates_a_subscriber
     */
    public function it_deletes_a_subscriber($existingUser)
    {
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/{$existingUser['id']}" =>
            Http::response([], 204),
        ]);

        //Executing the api
        $response = $this->json('DELETE', '/api/subscribers/' . $existingUser['id']);

        //Assertions
        $response->assertStatus(204);
    }

    /** 
     * @test
     * 
     */
    public function it_throws_error_while_deleting_invalid_subscriber()
    {
        $this->fakeApiValidation();
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.endpoint') . "subscribers/1" =>
            Http::response([], 404),
        ]);

        //Executing the api
        $response = $this->json('DELETE', '/api/subscribers/1');

        //Assertions
        $response->assertStatus(404);
    }

    /** 
     * Fake APIKey Validation
     *
     */
    protected function fakeApiValidation()
    {
        Http::fake([
            //Faking user exists response
            config('services.mailerlite.validation_endpoint') =>
            Http::response([], 200),
        ]);
    }
}
