<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb/app/UI/Admin/Dashboard/default.latte */
final class Template_65c0389cf4 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb/app/UI/Admin/Dashboard/default.latte';

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
		echo 'Administrace
';
	}
}
