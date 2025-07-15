<?php

namespace App\Services;

class BBNTService
{
    public function parseDeviceJson($json): array
    {
        return is_array($json) && isset($json['devices']) ? $json['devices'] : [];
    }

    public function parseProductJson($json): array
    {
        return is_array($json) && isset($json['products']) ? $json['products'] : [];
    }

}
