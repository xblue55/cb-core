<?php

namespace CayBua\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class CustomSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey == null) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey == null) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }
}