<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ContactDataTable;
use App\DataTables\LogSearchDataTable;
use App\DataTables\SubscribeEmailDataTable;
use App\Domain\Contact\Models\Contact;
use App\Domain\LogSearch\Models\LogSearch;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContactController
{
    use AuthorizesRequests;

    public function index(ContactDataTable $dataTable)
    {
        $this->authorize('view', Contact::class);
        return $dataTable->render('admin.contacts.index');
    }

    public function subscribeEmail(SubscribeEmailDataTable $dataTable)
    {
        $this->authorize('view', SubscribeEmail::class);
        return $dataTable->render('admin.contacts.subscribeEmail');
    }

    public function search(LogSearchDataTable $dataTable)
    {
        $this->authorize('view', LogSearch::class);
        return $dataTable->render('admin.contacts.logSearch');
    }
}
