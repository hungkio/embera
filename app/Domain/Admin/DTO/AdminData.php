<?php

declare(strict_types=1);

namespace App\Domain\Admin\DTO;

use App\Http\Requests\Admin\AdminRequest;
use Spatie\DataTransferObject\DataTransferObject;

class AdminData extends DataTransferObject
{
    public string $first_name;

    public string $last_name;

    public string $email;

    public ?string $password;

    public string $roles;

    public static function fromRequest(AdminRequest $request): AdminData
    {
        return new self([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'roles' => $request->input('roles'),
        ]);
    }
}
