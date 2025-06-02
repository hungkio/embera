<?php

namespace App\Domain\Acl\Actions;

use App\Domain\Acl\Models\Role;

class RoleBulkDeleteAction
{
    public function execute(array $ids): int
    {
        $deletedRecord = Role::whereIn('id', $ids)->delete();

        return $deletedRecord;
    }
}
