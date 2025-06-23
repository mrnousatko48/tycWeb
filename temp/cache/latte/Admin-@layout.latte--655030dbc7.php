<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb/app/UI/Admin/@layout.latte */
final class Template_655030dbc7 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb/app/UI/Admin/@layout.latte';

	public const Blocks = [
		['css' => 'blockCss'],
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autoškola PRIMA - Kvalitní výuka řidičských kurzů v Teplicích a Bílině.">
    <title>Autoškola PRIMA</title>
';
		$this->renderBlock('css', get_defined_vars()) /* line 8 */;
		echo '    <!-- Preload critical CSS -->
    <link rel="preload" href="/css/layout.css" as="style">
    <link rel="stylesheet" href="/css/layout.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
      <div class="container">
        <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Home:default')) /* line 25 */;
		echo '" class="navbar-brand">Autoškola PRIMA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Přepnout navigaci">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Home:default')) /* line 32 */;
		echo '" class="nav-link">Domů</a></li>
          </ul>
          <!-- Dropdown for logged-in user -->
';
		if ($user->isLoggedIn()) /* line 35 */ {
			echo '          <div class="ms-3">
            <div class="dropdown">
              <a class="btn btn-secondary dropdown-toggle" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                 ';
			echo LR\Filters::escapeHtmlText($user->identity->username) /* line 38 */;
			echo '
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Home:default')) /* line 41 */;
			echo '" class="dropdown-item text-primary">Domů</a></li>
                <li><a href="';
			echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link(':Front:Sign:out')) /* line 42 */;
			echo '" class="dropdown-item text-danger">Odhlásit se</a></li>
              </ul>
            </div>
          </div>
';
		}
		echo '        </div>
      </div>
    </nav>

';
		if ($flashes) /* line 50 */ {
			foreach ($flashes as $flash) /* line 51 */ {
				echo '        <div class="alert alert-';
				echo LR\Filters::escapeHtmlAttr($flash->type) /* line 52 */;
				echo ' alert-dismissible fade show" role="alert">
          ';
				echo LR\Filters::escapeHtmlText($flash->message) /* line 53 */;
				echo '
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zavřít"></button>
        </div>
';

			}

		}
		echo '
    <!-- Main Content -->
    <main class="container mt-5">
';
		$this->renderBlock('content', [], 'html') /* line 61 */;
		echo '    </main>

    <!-- Bootstrap JS (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>';
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['flash' => '51'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		return get_defined_vars();
	}


	/** {block css} on line 8 */
	public function blockCss(array $ʟ_args): void
	{
	}
}
