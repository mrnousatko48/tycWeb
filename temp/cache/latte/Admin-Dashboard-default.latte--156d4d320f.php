<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Admin/Dashboard/default.latte */
final class Template_156d4d320f extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Admin/Dashboard/default.latte';

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
			foreach (array_intersect_key(['order' => '20'], $this->params) as $ʟ_v => $ʟ_l) {
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

		echo '<h1>Seznam objednávek</h1>

';
		if (!$orders || count($orders) === 0) /* line 4 */ {
			echo '    <p>Žádné objednávky.</p>
';
		} else /* line 6 */ {
			echo '<table class="min-w-full table-auto border-collapse border border-gray-300">
    <thead>
        <tr>
            <th class="border border-gray-300 px-4 py-2">ID</th>
            <th class="border border-gray-300 px-4 py-2">Výrobce</th>
            <th class="border border-gray-300 px-4 py-2">Model</th>
            <th class="border border-gray-300 px-4 py-2">Barva</th>
            <th class="border border-gray-300 px-4 py-2">Krytka portu</th>
            <th class="border border-gray-300 px-4 py-2">Držák karet</th>
            <th class="border border-gray-300 px-4 py-2">Datum objednávky</th>
        </tr>
    </thead>
    <tbody>
';
			foreach ($orders as $order) /* line 20 */ {
				echo '        <tr>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($order->id) /* line 22 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($order->manufacturer) /* line 23 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($order->model) /* line 24 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($order->color) /* line 25 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				if ($order->port_cover) /* line 26 */ {
					echo 'Ano';
				} else /* line 26 */ {
					echo 'Ne';
				}
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($order->card_holder) /* line 27 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText(($this->filters->date)($order->created_at, 'j. n. Y H:i')) /* line 28 */;
				echo '</td>
        </tr>
';

			}

			echo '    </tbody>
</table>
';
		}
	}
}
