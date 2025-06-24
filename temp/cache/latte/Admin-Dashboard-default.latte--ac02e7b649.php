<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Admin/Dashboard/default.latte */
final class Template_ac02e7b649 extends Latte\Runtime\Template
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
			foreach (array_intersect_key(['orderData' => '18', 'case' => '27'], $this->params) as $ʟ_v => $ʟ_l) {
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
            <th class="border border-gray-300 px-4 py-2">ID objednávky</th>
            <th class="border border-gray-300 px-4 py-2">Uživatel</th>
            <th class="border border-gray-300 px-4 py-2">Pouzdra</th>
            <th class="border border-gray-300 px-4 py-2">Adresa</th>
            <th class="border border-gray-300 px-4 py-2">Datum objednávky</th>
        </tr>
    </thead>
    <tbody>
';
			foreach ($orders as $orderData) /* line 18 */ {
				echo '        <tr>
            <td class="border border-gray-300 px-4 py-2">';
				echo LR\Filters::escapeHtmlText($orderData['order']->id) /* line 20 */;
				echo '</td>
            <td class="border border-gray-300 px-4 py-2">
                ';
				echo LR\Filters::escapeHtmlText($orderData['user']?->email ?? $orderData['order']->user_id) /* line 22 */;
				echo '
            </td>
            <td class="border border-gray-300 px-4 py-2">
';
				if (count($orderData['cases']) > 0) /* line 25 */ {
					echo '                    <ul class="list-disc pl-5">
';
					foreach ($orderData['cases'] as $case) /* line 27 */ {
						echo '                            <li>
                                ';
						echo LR\Filters::escapeHtmlText($case->manufacturer) /* line 29 */;
						echo ' ';
						echo LR\Filters::escapeHtmlText($case->model) /* line 29 */;
						echo ' (';
						echo LR\Filters::escapeHtmlText($case->color) /* line 29 */;
						echo ')
                                ';
						if ($case->port_cover) /* line 30 */ {
							echo '(Krytka portu)';
						}
						echo '
                                ';
						if ($case->card_holder) /* line 31 */ {
							echo '(Držák karet)';
						}
						echo '
                            </li>
';

					}

					echo '                    </ul>
';
				} else /* line 35 */ {
					echo '                    Žádné pouzdra
';
				}
				echo '            </td>
            <td class="border border-gray-300 px-4 py-2">
                ';
				echo LR\Filters::escapeHtmlText($orderData['order']->address) /* line 40 */;
				echo ', ';
				echo LR\Filters::escapeHtmlText($orderData['order']->city) /* line 40 */;
				echo '
            </td>
            <td class="border border-gray-300 px-4 py-2">
                ';
				echo LR\Filters::escapeHtmlText(($this->filters->date)($orderData['order']->created_at, 'j. n. Y H:i')) /* line 43 */;
				echo '
            </td>
        </tr>
';

			}

			echo '    </tbody>
</table>
';
		}
	}
}
