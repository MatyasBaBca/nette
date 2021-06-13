<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;

class HomepagePresenter extends AdminBasePresenter
{
    public function __construct()
    {
    }

    public function renderDefault(): void
    {
        $this->template->headline = "Dashboard";
    }
}
