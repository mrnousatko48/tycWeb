<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Sign/up.latte */
final class Template_b6b3ee8531 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Sign/up.latte';

	public const Blocks = [
		['content' => 'blockContent', 'title' => 'blockTitle'],
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

		echo '<div class="text-center">
';
		$this->renderBlock('title', get_defined_vars()) /* line 3 */;
		echo '</div>

';
		$ʟ_tmp = $this->global->uiControl->getComponent('signUpForm');
		if ($ʟ_tmp instanceof Nette\Application\UI\Renderable) $ʟ_tmp->redrawControl(null, false);
		$ʟ_tmp->render() /* line 6 */;

		echo '<div class="text-center">
    <p class="fst-italic">*povinné údaje</p>
    <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('in')) /* line 9 */;
		echo '" class="btn btn-outline-success">Už máte účet? Přihlaste se.</a>
</div>';
	}


	/** n:block="title" on line 3 */
	public function blockTitle(array $ʟ_args): void
	{
		echo '    <h1>Registrace</h1>
';
	}
}
