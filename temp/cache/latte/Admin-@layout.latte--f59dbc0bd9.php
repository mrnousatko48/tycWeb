<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Admin/@layout.latte */
final class Template_f59dbc0bd9 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Admin/@layout.latte';

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
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

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

	<!-- Bootstrap CSS (CDN) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

	<link rel="stylesheet" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 17 */;
		echo '/assets/style.css" />
	<script src="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 18 */;
		echo '/assets/main.js" defer></script>
</head>

<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
			<div class="container">
				<a class="navbar-brand fw-bold" href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 25 */;
		echo '/">3D Kryty</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
				        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Home:default')) /* line 34 */;
		echo '" class="nav-link">Domů</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Produkty</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">O nás</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Kontakt</a>
						</li>
					</ul>

					<ul class="navbar-nav mb-2 mb-lg-0">
';
		if ($user->isLoggedIn()) /* line 48 */ {
			echo '							<li class="nav-item">
								<span class="navbar-text me-3">Přihlášen jako: <strong>';
			echo LR\Filters::escapeHtmlText($user->identity->username) /* line 50 */;
			echo '</strong></span>
							</li>
							<li class="nav-item">
								<a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Sign:out')) /* line 53 */;
			echo '" class="nav-link">Odhlásit se</a>
							</li>
';
		} else /* line 55 */ {
			echo '							<li class="nav-item">
								<a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Sign:in')) /* line 57 */;
			echo '" class="nav-link">Přihlášení</a>
							</li>
';
		}
		echo '					</ul>
				</div>
			</div>
		</nav>
	</header>

	<main class="container my-4">
';
		foreach ($flashes as $flash) /* line 67 */ {
			echo '			<div class="alert alert-';
			echo LR\Filters::escapeHtmlAttr($flash->type) /* line 68 */;
			echo '">';
			echo LR\Filters::escapeHtmlText(!$flash->message) /* line 68 */;
			echo '</div>
';

		}

		echo "\n";
		$this->renderBlock('content', [], 'html') /* line 71 */;
		echo '	</main>

	<!-- Bootstrap JS (Popper + Bootstrap) -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
';
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['flash' => '67'], $this->params) as $ʟ_v => $ʟ_l) {
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
