<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Profile/edit.latte */
final class Template_8380bde97c extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Profile/edit.latte';

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

		echo '<a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('default')) /* line 2 */;
		echo '" class="btn btn-primary">Zpět</a>
';
		$ʟ_tmp = $this->global->uiControl->getComponent('editProfileForm');
		if ($ʟ_tmp instanceof Nette\Application\UI\Renderable) $ʟ_tmp->redrawControl(null, false);
		$ʟ_tmp->render() /* line 3 */;

		echo "\n";
	}
}
