<?php

namespace App\Helpers;

use App\Facades\MailerLite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class Helper
{

    /**
     * Generate DataTable compatible response
     * 
     * @param Illuminate\Http\JsonResponse $data
     * @param int $draw
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getDataTableResponse(JsonResponse $data, int $draw): JsonResponse
    {
        $response = $data->getData();
        $response->draw = $draw;

        $response->recordsTotal = $response->recordsFiltered = 0;
        $isSubArray = self::containsSubArray((array)$response->data);
        //searching subscriber
        if (!$isSubArray) {
            $response->recordsTotal = $response->recordsFiltered = 1;
        } else {
            $totalRecords = MailerLite::get(0);
            if ($totalRecords->getData()->isSuccess) {
                $response->recordsTotal = $response->recordsFiltered = $totalRecords->getData()->data->total;
            }
        }

        $response->data = $isSubArray ? $response->data : [$response->data];
        return Response::json($response, $data->status());
    }

    /**
     * check if array contains sub array
     * 
     * @param array $array
     * 
     * @return array
     */
    public function containsSubArray(array $array)
    {
        //is array is empty return it as it is
        if (empty($array)) {
            return true;
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                return true;
            }
            return false;
        }
    }
}
