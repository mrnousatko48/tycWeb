<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Cart/order.latte */
final class Template_f68ffaad62 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Cart/order.latte';

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


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['case' => '29'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		return get_defined_vars();
	}


	/** {block content} on line 1 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);

		echo '<h1>Dokončení objednávky</h1>

<div class="row">
    <div class="col-md-6">
        <p>Zadejte doručovací údaje k objednávce:</p>
';
		$ʟ_tmp = $this->global->uiControl->getComponent('sendOrderForm');
		if ($ʟ_tmp instanceof Nette\Application\UI\Renderable) $ʟ_tmp->redrawControl(null, false);
		$ʟ_tmp->render() /* line 7 */;

		echo '    </div>

    <div class="col-md-6">
        <h2>Souhrn objednávky</h2>

';
		if (!$cases || count($cases) === 0) /* line 13 */ {
			echo '            <p>Košík je prázdný.</p>
';
		} else /* line 15 */ {
			echo '            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Výrobce</th>
                        <th>Model</th>
                        <th>Barva</th>
                        <th>Krytka portu</th>
                        <th>Držák karet</th>
                        <th>Množství</th>
                    </tr>
                </thead>
                <tbody>
';
			foreach ($cases as $case) /* line 29 */ {
				$qty = $quantities[$case->id] /* line 30 */;
				echo '                        <tr>
                            <td>';
				echo LR\Filters::escapeHtmlText($case->id) /* line 32 */;
				echo '</td>
                            <td>';
				echo LR\Filters::escapeHtmlText($case->manufacturer) /* line 33 */;
				echo '</td>
                            <td>';
				echo LR\Filters::escapeHtmlText($case->model) /* line 34 */;
				echo '</td>
                            <td>';
				echo LR\Filters::escapeHtmlText($case->color) /* line 35 */;
				echo '</td>
                            <td>';
				if ($case->port_cover) /* line 36 */ {
					echo 'Ano';
				} else /* line 36 */ {
					echo 'Ne';
				}
				echo '</td>
                            <td>';
				if ($case->card_holder) /* line 37 */ {
					echo 'Ano';
				} else /* line 37 */ {
					echo 'Ne';
				}
				echo '</td>
                            <td>';
				echo LR\Filters::escapeHtmlText($qty) /* line 38 */;
				echo '</td>
                        </tr>
';

			}

			echo '                </tbody>
            </table>
';
		}
		echo '    </div>
</div>
';
	}
}
