<?php
declare(strict_types=1);

namespace App\UI\Front;

use Nette\Application\UI\Presenter;
use App\Model\PageFacade;
use App\Model\OrderFacade;
use App\Model\UserFacade;
use App\Utils\ArrayTranslator;
use Nette\Localization\Translator;

abstract class BaseFrontPresenter extends Presenter
{
    protected OrderFacade $orderFacade;
    protected PageFacade $pageFacade;
    protected UserFacade $userFacade;
    protected Translator $translator;

    public function injectDependencies(OrderFacade $orderFacade, PageFacade $pageFacade, UserFacade $userFacade): void
    {
        $this->orderFacade = $orderFacade;
        $this->pageFacade = $pageFacade;
        $this->userFacade = $userFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        // Check for saved language in cookie
        $savedLang = $this->getHttpRequest()->getCookie('lang');
        $lang = $this->getParameter('lang', $savedLang);

        // If no lang parameter or saved language, detect from Accept-Language
        if (!$lang) {
            $acceptLanguage = $this->getHttpRequest()->getHeader('Accept-Language') ?: 'cs';
            $lang = $this->detectPreferredLanguage($acceptLanguage);
        }

        // Set translator and template language
        $this->translator = new ArrayTranslator($lang);
        $this->template->setTranslator($this->translator);
        $this->template->lang = $lang;

        // Persist language in cookie
        $this->getHttpResponse()->setCookie('lang', $lang, '30 days', '/', null, true, true, 'Strict');

        // Redirect to include lang in URL if not present
        if (!$this->getParameter('lang') && !$this->isAjax()) {
            $this->redirect('this', ['lang' => $lang]);
        }

        // Dark theme logic
        $savedTheme = $this->getHttpRequest()->getCookie('theme');
        if ($savedTheme === 'dark') {
            $this->template->darkMode = true;
        } elseif ($savedTheme === 'light') {
            $this->template->darkMode = false;
        } else {
            $this->template->darkMode = $this->isDarkModePreferred();
        }

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

        $shutdown = $this->userFacade->getSetting('shutdown');
            if ($shutdown === '1' && $this->getAction() !== 'shutdown') {
                echo 'Aplikace byla vypnuta vývojářem.';
                $this->terminate();
}
    }

    protected function detectPreferredLanguage(string $acceptLanguage): string
    {
        // Parse Accept-Language header (e.g., "en-US;q=0.9,cs-CZ;q=0.8")
        $languages = array_map('trim', explode(',', $acceptLanguage));
        $preferred = ['lang' => 'cs', 'quality' => 0.0]; // Default to cs

        foreach ($languages as $lang) {
            // Extract language code and quality (e.g., "en-US;q=0.9" -> ["en-US", 0.9])
            $parts = explode(';', $lang);
            $langCode = $parts[0];
            $quality = isset($parts[1]) ? (float) str_replace('q=', '', $parts[1]) : 1.0;

            // Check for supported languages (cs or en)
            if (strpos($langCode, 'cs') === 0 && $quality > $preferred['quality']) {
                $preferred = ['lang' => 'cs', 'quality' => $quality];
            } elseif (strpos($langCode, 'en') === 0 && $quality > $preferred['quality']) {
                $preferred = ['lang' => 'en', 'quality' => $quality];
            }
        }

        return $preferred['lang'];
    }

    protected function isDarkModePreferred(): bool
    {
        return $this->getHttpRequest()->isAjax() ? false : (bool)preg_match('/dark/', $this->getHttpRequest()->getHeader('Sec-CH-Prefers-Color-Scheme') ?: '');
    }

    public function handleToggleTheme(): void
    {
        $currentTheme = $this->getHttpRequest()->getCookie('theme') ?: ($this->isDarkModePreferred() ? 'dark' : 'light');
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';
        $this->getHttpResponse()->setCookie('theme', $newTheme, '30 days', '/', null, true, true, 'Strict');
        $this->redirect('this');
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->template->addFilter('translate', function ($value) {
            return $this->translator->translate($value);
        });
    }
}