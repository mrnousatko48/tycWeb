<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Cart/sendorder.latte */
final class Template_0a1ae2b639 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Cart/sendorder.latte';


	public function main(array $ʟ_args): void
	{
		extract($ʟ_args);
		unset($ʟ_args);

		if ($this->global->snippetDriver?->renderSnippets($this->blocks[self::LayerSnippet], $this->params)) {
			return;
		}

		echo '<h2>Vaše objednávky</h2>

';
		if ($orders) /* line 3 */ {
			echo '    <ul>
';
			foreach ($orders as $order) /* line 5 */ {
				echo '            <li>
                Objednávka č. ';
				echo LR\Filters::escapeHtmlText($order->id) /* line 7 */;
				echo ', vytvořeno: ';
				echo LR\Filters::escapeHtmlText(($this->filters->date)($order->created_at, 'j.n.Y H:i')) /* line 7 */;
				echo "\n";
				if (isset($order->address)) /* line 8 */ {
					echo '                <br>Adresa: ';
					echo LR\Filters::escapeHtmlText($order->address) /* line 8 */;
					echo ', ';
					echo LR\Filters::escapeHtmlText($order->city) /* line 8 */;
				}
				echo '
            </li>
';

			}

			echo '    </ul>
';
		} else /* line 12 */ {
			echo '    <p>Nemáte žádné objednávky.</p>
';
		}
		echo '
<hr>

<h3>Zadejte doručovací údaje</h3>
';
		$form = $this->global->formsStack[] = $this->global->uiControl['addressForm'] /* line 19 */;
		Nette\Bridges\FormsLatte\Runtime::initializeForm($form);
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form, []) /* line 19 */;
		echo '
    <div>
        ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('address', $this->global)->getLabel()) /* line 21 */;
		echo '
        ';
		echo Nette\Bridges\FormsLatte\Runtime::item('address', $this->global)->getControl() /* line 22 */;
		echo '
    </div>
    <div>
        ';
		echo ($ʟ_label = Nette\Bridges\FormsLatte\Runtime::item('city', $this->global)->getLabel()) /* line 25 */;
		echo '
        ';
		echo Nette\Bridges\FormsLatte\Runtime::item('city', $this->global)->getControl() /* line 26 */;
		echo '
    </div>
    <div>
        ';
		echo Nette\Bridges\FormsLatte\Runtime::item('submit', $this->global)->getControl() /* line 29 */;
		echo '
    </div>
';
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack)) /* line 31 */;

		echo "\n";
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['order' => '5'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		return get_defined_vars();
	}
}
