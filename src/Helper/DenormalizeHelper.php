<?php

namespace App\Helper;


class DenormalizeHelper
{
    /**
     * @param array|int $data
     * @return array
     */
    public function denormalize($data): array
    {
        if (is_int($data)) {
            $id         = $data;
            $data       = [];
            $data['id'] = $id;
        }

        return $data;
    }
}