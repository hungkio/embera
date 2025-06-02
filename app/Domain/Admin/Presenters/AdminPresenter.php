<?php


namespace App\Domain\Admin\Presenters;


trait AdminPresenter
{
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
