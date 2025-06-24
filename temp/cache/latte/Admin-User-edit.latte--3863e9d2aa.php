<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Admin/User/edit.latte */
final class Template_3863e9d2aa extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Admin/User/edit.latte';

	public const Blocks = [
		['content' => 'blockContent'],
	];


	public function main(array $ʟ_args): void
	{
		extract($ʟ_args);
		unset($ʟ_args);

		if ($this->global->snippetDriver?->renderSnippets($this->blocks[self::LayerSnippet], $this->params)) {
			return;
		}

		$this->renderBlock('content', get_defined_vars()) /* line 1 */;
	}


	/** {block content} on line 1 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);

		echo '<h1 class="text-2xl font-semibold mb-4">Upravit uživatele</h1>

<div class="max-w-xl">
    ';
		$form = $this->global->formsStack[] = $this->global->uiControl['editUserForm'] /* line 5 */;
		Nette\Bridges\FormsLatte\Runtime::initializeForm($form);
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form, []) /* line 5 */;
		echo '
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('username', $this->global)->getLabel()) /* line 7 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('username', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 7 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('firstname', $this->global)->getLabel()) /* line 10 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('firstname', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 10 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('lastname', $this->global)->getLabel()) /* line 13 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('lastname', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 13 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('email', $this->global)->getLabel()) /* line 16 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('email', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 16 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('address', $this->global)->getLabel()) /* line 19 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('address', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 19 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('city', $this->global)->getLabel()) /* line 22 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('city', $this->global)->getControl()->addAttributes(['class' => 'form-control']) /* line 22 */;
		echo '
        </div>
        <div class="mb-3">
            ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('role', $this->global)->getLabel()) /* line 25 */;
		echo ' ';
		echo Nette\Bridges\FormsLatte\Runtime::item('role', $this->global)->getControl()->addAttributes(['class' => 'form-select']) /* line 25 */;
		echo '
        </div>
        <div>
            ';
		echo Nette\Bridges\FormsLatte\Runtime::item('submit', $this->global)->getControl()->addAttributes(['class' => 'btn btn-primary']) /* line 28 */;
		echo '
        </div>
    ';
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack)) /* line 30 */;

		echo '
</div>
';
	}
}
