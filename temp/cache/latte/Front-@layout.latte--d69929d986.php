<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/@layout.latte */
final class Template_d69929d986 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/@layout.latte';

	public const Blocks = [
		['title' => 'blockTitle'],
	];


	public function main(array $ʟ_args): void
	{
		extract($ʟ_args);
		unset($ʟ_args);

		if ($this->global->snippetDriver?->renderSnippets($this->blocks[self::LayerSnippet], $this->params)) {
			return;
		}

		echo '<!DOCTYPE html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>
';
		if ($this->hasBlock('title')) /* line 8 */ {
			echo '			';
			$this->renderBlock('title', get_defined_vars()) /* line 9 */;
			echo ' | 
';
		}
		echo '		3D Kryty
	</title>

	<link rel="stylesheet" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 14 */;
		echo '/assets/style.css">
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 15 */;
		echo '/assets/main.js" defer></script>
</head>

<body>
	<header>
		<div class="container d-flex justify-content-between align-items-center">
			<h1><a href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 21 */;
		echo '/">3D Kryty</a></h1>
			<nav>
				<ul class="d-flex list-unstyled mb-0 align-items-center">
					<li><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:default')) /* line 24 */;
		echo '" class="me-3">Domů</a></li>
					<li><a href="#" class="me-3">Produkty</a></li>
					<li><a href="#" class="me-3">O nás</a></li>
					<li><a href="#" class="me-3">Kontakt</a></li>

';
		if ($user->isLoggedIn()) /* line 29 */ {
			echo '						<li class="me-3 text-muted">
							Přihlášen jako 
';
			if (isset($user->identity->username)) /* line 32 */ {
				echo '								<strong>';
				echo LR\Filters::escapeHtmlText($user->identity->username) /* line 33 */;
				echo '</strong>
';
			} else /* line 34 */ {
				echo '								<strong>(neznámý uživatel)</strong>
';
			}
			echo '						</li>
						<li><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Sign:out')) /* line 38 */;
			echo '">Odhlásit se</a></li>
';
		} else /* line 39 */ {
			echo '						<li><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Sign:in')) /* line 40 */;
			echo '">Přihlášení</a></li>
';
		}
		echo '				</ul>
			</nav>
		</div>
	</header>


	<main class="container">
';
		foreach ($flashes as $flash) /* line 49 */ {
			echo '			<div';
			echo ($ʟ_tmp = array_filter(['flash', $flash->type])) ? ' class="' . LR\Filters::escapeHtmlAttr(implode(" ", array_unique($ʟ_tmp))) . '"' : "" /* line 50 */;
			echo '>';
			echo LR\Filters::escapeHtmlText($flash->message) /* line 50 */;
			echo '</div>
';

		}

		echo "\n";
		$this->renderBlock('content', [], 'html') /* line 53 */;
		echo '	</main>

</body>
</html>
';
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['flash' => '49'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		return get_defined_vars();
	}


	/** {block title} on line 9 */
	public function blockTitle(array $ʟ_args): void
	{
	}
}
