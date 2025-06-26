<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Profile/default.latte */
final class Template_557a6bf09f extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Profile/default.latte';

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

		echo '  <dl>
    <dt>Uživatelské jméno:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->username) /* line 4 */;
		echo '</dd>

    <dt>Jméno:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->firstname) /* line 7 */;
		echo '</dd>

    <dt>Příjmení:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->lastname) /* line 10 */;
		echo '</dd>

    <dt>Email:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->email) /* line 13 */;
		echo '</dd>

    <dt>Adresa:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->address) /* line 16 */;
		echo '</dd>

    <dt>Město:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->city) /* line 19 */;
		echo '</dd>
      
    <dt>PSČ:</dt>
    <dd>';
		echo LR\Filters::escapeHtmlText($profileUser->psc) /* line 22 */;
		echo '</dd>

</dl>
<a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('edit')) /* line 25 */;
		echo '" class="btn btn-outline-success">Upravit profil</a>

  
';
	}
}
