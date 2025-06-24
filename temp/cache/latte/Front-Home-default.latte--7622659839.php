<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Home/default.latte */
final class Template_7622659839 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Home/default.latte';

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

		echo '<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobilní Kryty - Úvod</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .fixed-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .product-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Hlavní úvodní sekce -->
    <section class="min-h-screen flex items-center justify-center bg-gradient-to-b from-gray-50 to-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">3D tisknuté kryty na míru</h1>
            <p class="text-lg md:text-xl text-gray-600 mb-8">Navrhněte si jedinečný kryt na telefon s využitím pokročilé 3D tiskové technologie.</p>
            <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:detail')) /* line 31 */;
		echo '" class="inline-block bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition text-lg">Navrhnout kryt</a>
        </div>
    </section>

    <!-- Sekce o technologii 3D tisku -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-4">Pokročilá 3D tisková technologie</h2>
                    <p class="text-lg text-gray-600 mb-4">Naše kryty jsou vyráběny pomocí nejmodernějších 3D tiskáren, které zajišťují precizní zpracování a vysokou odolnost.</p>
                    <p class="text-lg text-gray-600">Díky 3D tisku můžeme nabídnout nejen unikátní design, ale i perfektní přizpůsobení vašemu telefonu. Každý kryt je vyroben z ekologických materiálů s důrazem na kvalitu.</p>
                </div>
                <div>
                    <img src="/uploads/3Dprint.webp" alt="3D tiskárna" class="fixed-img rounded-lg shadow-md">
                </div>
            </div>
        </div>
    </section>

    <!-- Sekce o customizaci -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <img src="/uploads/custom.png" alt="Customizace krytu" class="fixed-img rounded-lg shadow-md">
                </div>
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-4">Vytvořte si kryt podle svého</h2>
                    <p class="text-lg text-gray-600 mb-4">Přizpůsobte si kryt přesně podle svých představ. Vyberte barvu, přidejte krytku nabíjecího portu nebo držák na karty.</p>
                    <p class="text-lg text-gray-600">S naším intuitivním konfigurátorem si navrhnete kryt, který dokonale odráží váš styl a potřeby.</p>
                    <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:default')) /* line 62 */;
		echo '" class="mt-4 inline-block bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition">Začít navrhovat</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sekce s ukázkou produktů -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-semibold text-gray-900 text-center mb-12">Inspirujte se našimi kryty</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <!-- Produkt 1 -->
                <div class="group">
                    <img src="/uploads/black.jpeg" alt="Černý kryt" class="product-img rounded-lg shadow-md group-hover:shadow-lg transition">
                    <h3 class="mt-4 text-xl font-medium text-gray-900">Černý kryt</h3>
                    <p class="text-gray-600">599 Kč</p>
                    <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:detail')) /* line 78 */;
		echo '" class="mt-2 inline-block text-gray-600 hover:text-gray-900">Zobrazit detaily</a>
                </div>
                <!-- Produkt 2 -->
                <div class="group">
                    <img src="/uploads/blue.jpeg" alt="Modrý kryt" class="product-img rounded-lg shadow-md group-hover:shadow-lg transition">
                    <h3 class="mt-4 text-xl font-medium text-gray-900">Modrý kryt</h3>
                    <p class="text-gray-600">599 Kč</p>
                    <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:detail')) /* line 85 */;
		echo '" class="mt-2 inline-block text-gray-600 hover:text-gray-900">Zobrazit detaily</a>
                </div>
                <!-- Produkt 3 -->
                <div class="group">
                    <img src="/uploads/white.jpg" alt="Bílý kryt" class="product-img rounded-lg shadow-md group-hover:shadow-lg transition">
                    <h3 class="mt-4 text-xl font-medium text-gray-900">Bílý kryt</h3>
                    <p class="text-gray-600">599 Kč</p>
                    <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:detail')) /* line 92 */;
		echo '" class="mt-2 inline-block text-gray-600 hover:text-gray-900">Zobrazit detaily</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sekce s výzvou k akci -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-semibold text-gray-900 mb-4">Váš kryt, váš styl</h2>
            <p class="text-lg text-gray-600 mb-8">S 3D tiskem si navrhnete kryt přesně podle svých představ.</p>
            <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:default')) /* line 103 */;
		echo '" class="inline-block bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition text-lg">Začít nyní</a>
        </div>
    </section>
</body>
</html>
';
	}
}
