<?php

declare(strict_types=1);

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Models\Admin;

class BulkDeleteAction
{
    public function execute(array $ids): int
    {
        $deletedRecord = Admin::whereIn('id', $ids)->where('id', '<>', 1)->delete();

        return $deletedRecord;
    }
}
