<?php

declare(strict_types=1);

namespace App\UI\Accessory;

trait RequireLoggedUser
{
	public function injectRequireLoggedUser(): void
	{
		$this->onStartup[] = function () {
			$user = $this->getUser();

			if ($user->isLoggedIn() && in_array($user->identity->role, ['ADMIN', 'CLEN'], true)) {
				return;
			} elseif (!$user->isLoggedIn() || !in_array($user->identity->role, ['ADMIN', 'CLEN'], true)) {
				$this->flashMessage('Do této sekce nemáš oprávnění', 'danger');
				$this->redirect(':Front:Sign:in');
			} elseif ($user->getLogoutReason() === $user::LogoutInactivity) {
				$this->flashMessage('Proběhlo odhlášení kvůli neaktivitě', 'info');
				$this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
			}
		};
	}
}
