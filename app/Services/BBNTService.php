<?php

namespace App\Services;

use App\Models\Shop;

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

    public function convertToDeviceJson(array $rows): array
    {
        return [
            'devices' => array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? '',
                    'code' => $row['code'] ?? '',
                    'unit' => $row['unit'] ?? 'Máy',
                    'quantity' => (int)($row['quantity'] ?? 0),
                    'note' => $row['note'] ?? '',
                ];
            }, $rows)
        ];
    }

    public function convertToProductJson(array $rows): array
    {
        return [
            'products' => array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? '',
                    'code' => $row['code'] ?? '',
                    'unit' => $row['unit'] ?? '',
                    'quantity' => (int)($row['quantity'] ?? 0),
                    'note' => $row['note'] ?? '',
                ];
            }, $rows)
        ];
    }

    public function summarizeDevices(?array $json): array
    {
        if (!is_array($json)) return [];

        $devices = $json['devices'] ?? [];
        $summary = [];

        foreach ($devices as $device) {
            $name = strtoupper(trim($device['name'] ?? ''));
            $code = trim($device['code'] ?? '');
            $pin = $device['pin'] ?? '-';
            $unit = $device['unit'] ?? '';
            $note = $device['note'] ?? '';

            $key = $name . '_' . $pin;

            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'name' => $name,
                    'codes' => [],
                    'pin' => $pin,
                    'unit' => $unit,
                    'note' => $note,
                ];
            }

            if ($code) {
                $summary[$key]['codes'][] = $code;
            }
        }

        foreach ($summary as &$item) {
            $item['codes'] = implode(', ', array_unique($item['codes']));
        }

        return array_values($summary);
    }
}
