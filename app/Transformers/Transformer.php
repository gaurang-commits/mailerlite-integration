<?php

namespace App\Transformers;

abstract class Transformer
{

    /**
     * Transform items
     * 
     * @param array $items
     * 
     * @return array
     */
    public function transformCollection(array $items)
    {
        return [
            'data' => array_map([$this, 'transform'], $items['data']),
            'links' => $items['links']
        ];
    }


    public abstract function transform(array $item);
}
