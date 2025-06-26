<?php

use Latte\Runtime as LR;

/** source: /Users/dostals/tycWeb-1/app/UI/Front/Home/detail.latte */
final class Template_c896cf0419 extends Latte\Runtime\Template
{
	public const Source = '/Users/dostals/tycWeb-1/app/UI/Front/Home/detail.latte';

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

		echo '    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            @apply bg-gray-50;
        }
        .selected {
            @apply ring-2 ring-black ring-offset-2;
        }
        header {
            @apply fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md shadow-sm z-50 transition-all duration-300;
        }
        header.scrolled {
            @apply bg-white/95 shadow-md;
        }
        footer {
            @apply bg-gray-800 text-white py-16;
        }
        .content-container {
            @apply pt-24 pb-16 min-h-screen; /* Adjusted for taller header */
        }
        .logo {
            @apply text-2xl font-bold text-gray-900 tracking-tight;
        }
        nav a {
            @apply relative text-sm font-medium text-gray-700 hover:text-black transition-colors;
        }
        nav a::after {
            @apply absolute bottom-0 left-0 w-0 h-0.5 bg-black transition-all duration-300;
            content: \'\';
        }
        nav a:hover::after {
            @apply w-full;
        }
        footer a {
            @apply text-gray-300 hover:text-white transition-colors;
        }
        footer h3 {
            @apply text-base font-semibold tracking-wide;
        }
        .footer-divider {
            @apply border-t border-gray-700;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="content-container flex items-center justify-center">
        <div class="max-w-5xl w-full mx-auto p-6">
            <!-- Hlavní kontejner -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Náhled produktu -->
                <div class="flex justify-center items-center">
                    <img id="product-image" src="/uploads/black.jpeg" alt="Náhled krytu" class="max-w-full h-auto rounded-lg shadow-lg">
                </div>

                <!-- Konfigurační možnosti -->
                <div class="space-y-6">
                    <h1 class="text-3xl font-semibold text-gray-900">Kryt na telefon</h1>
                    <p class="text-lg text-gray-600">Vyberte si svůj styl a přizpůsobte si kryt podle svých potřeb.</p>
                    <p class="text-xl font-medium text-gray-900">Cena: 599 Kč</p>
                    <p class="text-sm text-gray-500">Kompatibilní s: <span id="selected-device">Vyberte výrobce a model</span></p>

                    <!-- Výběr výrobce a modelu -->
                    <div>
                        <h2 class="text-xl font-medium text-gray-900">Model telefonu</h2>
                        <div class="mt-2 flex space-x-3">
                            <select id="manufacturer" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-black transition" onchange="updateModels()">
                                <option value="" disabled selected>Vyberte výrobce</option>
                                <option value="apple">Apple</option>
                                <option value="samsung">Samsung</option>
                                <option value="xiaomi">Xiaomi</option>
                            </select>
                            <select id="model" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-black transition" disabled>
                                <option value="" disabled selected>Vyberte model</option>
                            </select>
                        </div>
                    </div>

                    <!-- Barva -->
                    <div>
                        <h2 class="text-xl font-medium text-gray-900">Barva</h2>
                        <div class="mt-2 flex space-x-3">
                            <button data-color="Černá" data-image="https://via.placeholder.com/400x600/000000/FFFFFF?text=Černý+kryt" class="w-10 h-10 rounded-full bg-black hover:ring-2 hover:ring-offset-2 hover:ring-gray-400 selected transition" title="Černá" onclick="changeImage(this)"></button>
                            <button data-color="Bílá" data-image="https://via.placeholder.com/400x600/FFFFFF/000000?text=Bílý+kryt" class="w-10 h-10 rounded-full bg-white border border-gray-300 hover:ring-2 hover:ring-offset-2 hover:ring-gray-400 transition" title="Bílá" onclick="changeImage(this)"></button>
                            <button data-color="Modrá" data-image="https://via.placeholder.com/400x600/3B82F6/FFFFFF?text=Modrý+kryt" class="w-10 h-10 rounded-full bg-blue-500 hover:ring-2 hover:ring-offset-2 hover:ring-gray-400 transition" title="Modrá" onclick="changeImage(this)"></button>
                            <button data-color="Červená" data-image="https://via.placeholder.com/400x600/EF4444/FFFFFF?text=Červený+kryt" class="w-10 h-10 rounded-full bg-red-500 hover:ring-2 hover:ring-offset-2 hover:ring-gray-400 transition" title="Červená" onclick="changeImage(this)"></button>
                        </div>
                    </div>

                    <!-- Krytka nabíjecího portu -->
                    <div>
                        <h2 class="text-xl font-medium text-gray-900">Krytka nabíjecího portu</h2>
                        <div class="mt-2 flex space-x-3">
                            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 selected transition" onclick="toggleSelection(this)">Ano</button>
                            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition" onclick="toggleSelection(this)">Ne</button>
                        </div>
                    </div>

                    <!-- Držák karet -->
                    <div>
                        <h2 class="text-xl font-medium text-gray-900">Držák karet</h2>
                        <div class="mt-2 flex space-x-3">
                            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 selected transition" onclick="toggleSelection(this)">1 slot</button>
                            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition" onclick="toggleSelection(this)">2 sloty</button>
                            <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition" onclick="toggleSelection(this)">Žádný</button>
                        </div>
                    </div>

                    <!-- Tlačítko přidat do košíku -->
                    <div>
                        <button class="w-full py-3 bg-black text-white rounded-lg hover:bg-gray-800 focus:ring-2 focus:ring-black transition">Přidat do košíku</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data pro modely podle výrobce
        const modelsByManufacturer = {
            apple: [\'iPhone 13\', \'iPhone 14\', \'iPhone 15\'],
            samsung: [\'Galaxy S22\', \'Galaxy S23\', \'Galaxy Z Fold 5\'],
            xiaomi: [\'Mi 12\', \'Mi 13\', \'Redmi Note 12\']
        };

        function updateModels() {
            const manufacturerSelect = document.getElementById(\'manufacturer\');
            const modelSelect = document.getElementById(\'model\');
            const selectedDevice = document.getElementById(\'selected-device\');

            // Vyčištění model selectu
            modelSelect.innerHTML = \'<option value="" disabled selected>Vyberte model</option>\';
            modelSelect.disabled = false;

            // Naplnění model selectu podle výrobce
            const selectedManufacturer = manufacturerSelect.value;
            if (selectedManufacturer && modelsByManufacturer[selectedManufacturer]) {
                modelsByManufacturer[selectedManufacturer].forEach(model => {
                    const option = document.createElement(\'option\');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            } else {
                modelSelect.disabled = true;
            }

            // Aktualizace zobrazení vybraného zařízení
            modelSelect.onchange = function() {
                if (modelSelect.value) {
                    selectedDevice.textContent = `${manufacturerSelect.options[manufacturerSelect.selectedIndex].text} ${modelSelect.value}`;
                } else {
                    selectedDevice.textContent = \'Vyberte výrobce a model\';
                }
            };
        }

        function changeImage(button) {
            document.querySelectorAll(\'[data-color]\').forEach(btn => btn.classList.remove(\'selected\'));
            button.classList.add(\'selected\');
            const image = document.getElementById(\'product-image\');
            image.src = button.dataset.image;
            image.alt = `Náhled krytu - ${button.dataset.color}`;
        }

        function toggleSelection(button) {
            button.parentElement.querySelectorAll(\'button\').forEach(btn => btn.classList.remove(\'selected\'));
            button.classList.add(\'selected\');
        }

        // Dynamické přidání třídy při scrollování
        window.addEventListener(\'scroll\', () => {
            const header = document.getElementById(\'header\');
            if (window.scrollY > 50) {
                header.classList.add(\'scrolled\');
            } else {
                header.classList.remove(\'scrolled\');
            }
        });
    </script>
';
	}
}
