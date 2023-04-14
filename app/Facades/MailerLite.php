<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static Illuminate\Http\JsonResponse get(int $limit = 100, string $cursor = '')
 * @method static Illuminate\Http\JsonResponse create(array $subscriber)
 * @method static Illuminate\Http\JsonResponse delete(string $id)
 * @method static Illuminate\Http\JsonResponse update(string $id, array $data)
 * @method static Illuminate\Http\JsonResponse find(string $email)
 *
 * @see App\Http\Services\MailerLiteService
 * @see App\Http\Services\ApiService
 */
class MailerLite extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MailerLite';
    }
}
