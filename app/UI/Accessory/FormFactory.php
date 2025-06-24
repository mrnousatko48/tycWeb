<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette;
use Nette\Application\UI\Form;

final class FormFactory
{
	public function __construct(
		private Nette\Security\User $user,
	) {
	}

	public function create(): Form
	{
		$form = new Form;
		if ($this->user->isLoggedIn()) {
			$form->addProtection();
		}
		return $form;
	}
}
