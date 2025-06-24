<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Cart/default.latte */
final class Template_e8d58d64da extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Cart/default.latte';

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
			foreach (array_intersect_key(['order' => '21'], $this->params) as $ʟ_v => $ʟ_l) {
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

		echo '
<h1>Moje objednávky</h1>

';
		if (!$orders || $orders->count() === 0) /* line 5 */ {
			echo '    <p>Nemáte žádné objednávky.</p>
';
		} else /* line 7 */ {
			echo '    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Výrobce</th>
                <th>Model</th>
                <th>Barva</th>
                <th>Krytka portu</th>
                <th>Držák karet</th>
                <th>Datum objednávky</th>
            </tr>
        </thead>
        <tbody>
';
			foreach ($orders as $order) /* line 21 */ {
				echo '                <tr>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->id) /* line 23 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->manufacturer) /* line 24 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->model) /* line 25 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->color) /* line 26 */;
				echo '</td>
                    <td>';
				if ($order->port_cover) /* line 27 */ {
					echo 'Ano';
				} else /* line 27 */ {
					echo 'Ne';
				}
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->card_holder) /* line 28 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($order->created_at->format('j. n. Y H:i')) /* line 29 */;
				echo '</td>
                </tr>
';

			}

			echo '        </tbody>
    </table>
';
		}
	}
}
