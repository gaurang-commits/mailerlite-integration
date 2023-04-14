<?php

namespace App\Transformers;

use Carbon\Carbon;

class SubscriberTransformer extends Transformer
{
    /**
     * Transform array according to required subscriber details
     * 
     * @param array $subscriber
     */
    public function transform(array $subscriber)
    {
        return [
            'id' => $subscriber['id'],
            'name' => $subscriber['fields']['name'],
            'email' => $subscriber['email'],
            'country' => $subscriber['fields']['country'],
            'subscribedOn' => Carbon::parse($subscriber['subscribed_at'])->format('d/m/y'),
            'subscribedAt' => Carbon::parse($subscriber['subscribed_at'])->format('H:i:s'),
        ];
    }
}
