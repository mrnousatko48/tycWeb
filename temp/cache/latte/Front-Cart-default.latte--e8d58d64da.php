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
			foreach (array_intersect_key(['case' => '22'], $this->params) as $ʟ_v => $ʟ_l) {
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

		echo '<h1>Košík</h1>

';
		if (!$cases || $cases->count() === 0) /* line 4 */ {
			echo '    <p>Nemáte žádné kryty v košíku.</p>
';
		} else /* line 6 */ {
			echo '    <form action="';
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($presenter->link('CreateOrder'))) /* line 7 */;
			echo '" method="post">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Výrobce</th>
                <th>Model</th>
                <th>Barva</th>
                <th>Krytka portu</th>
                <th>Držák karet</th>
                <th>Datum</th>
                <th>Množství</th>
            </tr>
            </thead>
            <tbody>
';
			foreach ($cases as $case) /* line 22 */ {
				echo '                <tr>
                    <td>';
				echo LR\Filters::escapeHtmlText($case->id) /* line 24 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($case->manufacturer) /* line 25 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($case->model) /* line 26 */;
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText($case->color) /* line 27 */;
				echo '</td>
                    <td>';
				if ($case->port_cover) /* line 28 */ {
					echo 'Ano';
				} else /* line 28 */ {
					echo 'Ne';
				}
				echo '</td>
                    <td>';
				if ($case->card_holder) /* line 29 */ {
					echo 'Ano';
				} else /* line 29 */ {
					echo 'Ne';
				}
				echo '</td>
                    <td>';
				echo LR\Filters::escapeHtmlText(($this->filters->date)($case->created_at, 'j. n. Y H:i')) /* line 30 */;
				echo '</td>
                    <td>
                        <input type="number" name="quantities[';
				echo LR\Filters::escapeHtmlAttr($case->id) /* line 32 */;
				echo '][amount]" value="1" min="1" class="form-control" style="width: 80px;">
                    </td>
                </tr>
';

			}

			echo '            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Pokračovat k objednávce</button>
    </form>
';
		}
	}
}
