<?php

namespace App\Http\Controllers\API;

use App\Facades\MailerLite;
use Helper;
use App\Http\Requests\CreateSubscriber;
use App\Http\Requests\UpdateSubscriber;
use Exception;
use Illuminate\Http\Request;

class ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //If request contains email to search a subscriber
        if (isset($request->search['value'])) {
            //find subscriber by email
            $data = MailerLite::find($request->search['value']);
        } else {

            //set cursor for pagination
            $cursor = $request->prev;
            if (($request->start >= $request->length) && $request->next) {
                $cursor = $request->next;
            }
            //get list of all subscribers
            $data = MailerLite::get($request->length, $cursor);
        }
        //return response compatible with data table
        return Helper::getDataTableResponse($data, $request->draw);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\UpdateSubscriber  $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(CreateSubscriber $request)
    {
        //create a subscriber
        return  MailerLite::create([
            'email' => $request->email,
            'fields' => [
                'name' => $request->name,
                'country' => $request->country,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateSubscriber  $request
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriber $request, $id)
    {
        //prepare request for update
        $data = [
            'fields' => [
                'name' => $request->name,
                'country' => $request->country
            ]
        ];
        //update the subscriber
        return MailerLite::update($id, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        //delete the subscriber
        return MailerLite::delete($id);
    }
}
