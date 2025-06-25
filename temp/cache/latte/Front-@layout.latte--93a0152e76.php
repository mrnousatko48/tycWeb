<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/@layout.latte */
final class Template_93a0152e76 extends Latte\Runtime\Template
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

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<link rel="stylesheet" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 17 */;
		echo '/assets/style.css">
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 18 */;
		echo '/assets/main.js" defer></script>
</head>

<body class="d-flex flex-column min-vh-100">
	<header class="bg-light shadow-sm py-3 mb-4">
		<div class="container d-flex justify-content-between align-items-center">
			<h1 class="h4 mb-0">
				<a href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 25 */;
		echo '/" class="text-decoration-none text-dark">3D Kryty</a>
			</h1>

			<nav>
				<ul class="nav">
					<li class="nav-item"><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:default')) /* line 30 */;
		echo '" class="nav-link">Domů</a></li>
					<li class="nav-item"><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:case')) /* line 31 */;
		echo '" class="nav-link">Objednat</a></li>
					<li class="nav-item"><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Cart:default')) /* line 32 */;
		echo '" class="nav-link">Košík</a></li>
					<li class="nav-item"><a href="#" class="nav-link">Kontakt</a></li>

';
		if ($user->isLoggedIn()) /* line 35 */ {
			echo '						<li class="nav-item d-flex align-items-center ms-3 text-muted">
							Přihlášen jako 
							<span class="ms-1 fw-semibold">
';
			if (isset($user->identity->username)) /* line 39 */ {
				echo '									';
				echo LR\Filters::escapeHtmlText($user->identity->username) /* line 40 */;
				echo "\n";
			} else /* line 41 */ {
				echo '									(neznámý uživatel)
';
			}
			echo '							</span>
						</li>
						<li class="nav-item"><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Profile:default')) /* line 46 */;
			echo '" class="nav-link">Profil</a></li>
						<li class="nav-item"><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Sign:out')) /* line 47 */;
			echo '" class="nav-link text-danger ms-2">Odhlásit se</a></li>
';
		} else /* line 48 */ {
			echo '						<li class="nav-item"><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Sign:in')) /* line 49 */;
			echo '" class="nav-link">Přihlášení</a></li>
';
		}
		echo '				</ul>
			</nav>
		</div>
	</header>

	<main class="container mb-5">
';
		foreach ($flashes as $flash) /* line 57 */ {
			echo '			<div class="alert alert-';
			echo LR\Filters::escapeHtmlAttr($flash->type) /* line 58 */;
			echo '">';
			echo LR\Filters::escapeHtmlText($flash->message) /* line 58 */;
			echo '</div>
';

		}

		echo "\n";
		$this->renderBlock('content', [], 'html') /* line 61 */;
		echo '	</main>

	<footer class="bg-light text-center text-muted py-3 mt-auto">
		<div class="container">
			&copy; ';
		echo LR\Filters::escapeHtmlText(date('Y')) /* line 66 */;
		echo ' 3D Kryty. Všechna práva vyhrazena.
		</div>
	</footer>

	<!-- Bootstrap JS (optional) -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
';
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['flash' => '57'], $this->params) as $ʟ_v => $ʟ_l) {
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
