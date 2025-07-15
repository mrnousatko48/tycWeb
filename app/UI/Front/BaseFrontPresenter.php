<?php

namespace App\UI\Front;

use Nette\Application\UI\Presenter;
use App\Model\PageFacade;
use App\Model\OrderFacade;
use Nette\Http\Session;

abstract class BaseFrontPresenter extends Presenter
{
    protected OrderFacade $orderFacade;
    protected PageFacade $pageFacade;

    public function injectDependencies(OrderFacade $orderFacade, PageFacade $pageFacade): void
    {
        $this->orderFacade = $orderFacade;
        $this->pageFacade = $pageFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        // Calculate cartCount for all front presenters
        if ($this->getUser()->isLoggedIn()) {
            $userId = (int) $this->getUser()->getId();
            $cartItems = $this->orderFacade->getCartCasesByUserId($userId);
            $cartCount = count($cartItems);
        } else {
            $session = $this->getSession('order');
            $quantities = $session->quantities ?? [];
            $cartCount = array_sum($quantities);
        }

        $this->template->cartCount = $cartCount;

        // Fetch logo paths for light and dark themes
        $this->template->logos = $this->pageFacade->getLogos();

        // Detect and set dark mode
        $savedTheme = $this->getHttpRequest()->getCookie('theme');
        if ($savedTheme === 'dark') {
            $this->template->darkMode = true;
        } elseif ($savedTheme === 'light') {
            $this->template->darkMode = false;
        } else {
            $this->template->darkMode = $this->isDarkModePreferred();
        }
    }

    public function handleToggleTheme(): void
    {
        $currentTheme = $this->getHttpRequest()->getCookie('theme') ?: ($this->isDarkModePreferred() ? 'dark' : 'light');
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';
        $this->getHttpResponse()->setCookie('theme', $newTheme, '30 days');
        $this->redirect('this');
    }

    protected function isDarkModePreferred(): bool
    {
        return $this->getHttpRequest()->isAjax()
            ? false
            : (bool)preg_match('/dark/', $this->getHttpRequest()->getHeader('Sec-CH-Prefers-Color-Scheme') ?: '');
    }
}
