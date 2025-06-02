<?php

declare(strict_types=1);

namespace App\Domain\Admin\DTO;

use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Support\Str;
use Spatie\DataTransferObject\DataTransferObject;

class RoleData extends DataTransferObject
{
    public string $name;

    public array $permissions;

    public string $display_name;

    public static function fromRequest(RoleRequest $request): RoleData
    {
        return new self([
            'display_name' => $request->input('display_name'),
            'permissions' => $request->allowPermissions(),
            'name' => Str::slug($request->input('display_name'), '.'),
        ]);
    }
}
