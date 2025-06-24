<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Admin/User/default.latte */
final class Template_678e0e5386 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Admin/User/default.latte';

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

		echo '<h1 class="text-2xl font-semibold mb-4">Seznam uživatelů</h1>
<div>
';
		$ʟ_tmp = $this->global->uiControl->getComponent('usersGrid');
		if ($ʟ_tmp instanceof Nette\Application\UI\Renderable) $ʟ_tmp->redrawControl(null, false);
		$ʟ_tmp->render() /* line 4 */;

		echo '</div>

<script>
    document.addEventListener(\'DOMContentLoaded\', () => {
        document.querySelectorAll(\'.clickable-row\').forEach(row => {
            row.addEventListener(\'click\', () => {
                const href = row.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
';
	}
}
