<?php
declare(strict_types=1);

namespace App\UI\Front\Test;

use Nette\Application\UI\Form;
use App\UI\Front\BaseFrontPresenter;


final class TestPresenter extends BaseFrontPresenter
{

    public function injectModelFacade(): void
    {
    }

public function renderDefault(): void
{
    // Zachytíme výstup funkce phpinfo() do proměnné
    ob_start();
    phpinfo();
    $phpInfo = ob_get_clean();

    // Předáme do šablony jako HTML string
    $this->template->phpInfo = $phpInfo;
}

}