<?php

declare(strict_types=1);

namespace App\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CurrentUserComposer
{
    /**
     * Bind data to view.
     * @param View $view
     */
    public function compose(View $view): void
    {
        $view->withCurrentUser(Auth::user());
    }
}
