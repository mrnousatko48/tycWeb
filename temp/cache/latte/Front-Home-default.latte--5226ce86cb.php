<?php

use Latte\Runtime as LR;

/** source: /root/tycWeb/app/UI/Front/Home/default.latte */
final class Template_5226ce86cb extends Latte\Runtime\Template
{
	public const Source = '/root/tycWeb/app/UI/Front/Home/default.latte';

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

		echo '<style>
    .fixed-img {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        aspect-ratio: 16 / 9;
    }
    .banner-section {
        min-height: 100vh;
        background: url("uploads/home/2.jpg") no-repeat center/cover;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .banner-content {
        text-align: center;
        z-index: 1;
        padding: 1.5rem;
        max-width: 80%;
    }
    .section-img {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        aspect-ratio: 4 / 3;
        border-radius: 0.75rem;
    }
    [data-theme="light"] .banner-section .banner-content h1,
    [data-theme="light"] .banner-section .banner-content p {
        color: white;
    }
    /* Gallery Styles */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, auto);
        gap: 1.5rem;
        padding: 1rem 0;
    }
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-item img {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        aspect-ratio: 4 / 3;
        border-radius: 0.75rem;
        display: block;
    }
    .gallery-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    .contact {
    background-color: #f8f9fa;
    padding: 4rem 0;
    border-top: 1px solid #e5e7eb; /* subtle top border */
}

.contact h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
    color: #1f2937; /* dark neutral text */
}

.contact p {
    font-size: 1.125rem;
    color: #4b5563; /* subtle gray for text */
}

.contact .contact-info {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact .contact-info:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.contact .contact-info p {
    font-size: 1.1rem;
    margin-bottom: 1.2rem;
    display: flex;
    align-items: center;
}

.contact .contact-info i {
    color: #007bff;
    font-size: 1.25rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.contact .contact-info a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease, text-decoration 0.3s ease;
}

.contact .contact-info a:hover {
    text-decoration: underline;
    color: #0056b3;
}

.contact iframe {
    border-radius: 0.75rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
 
</style>

<section class="banner-section relative">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="banner-content relative z-10 max-w-5xl mx-auto">
        <div class="banner-content max-w-5xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Odolné 3D tisknuté kryty</h1>
            <p class="text-lg md:text-xl mb-6 max-w-2xl mx-auto">
                Vytvořte si pevný a stylový kryt s pokročilou 3D tiskovou technologií a vlastním designem.
            </p>
            <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:detail')) /* line 131 */;
		echo '" class="btn btn-primary shadow-md hover:shadow-lg">
                Navrhnout kryt
            </a>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div>
                <h2 class="text-3xl font-semibold mb-3">Odolnost navržená pro život</h2>
                <p class="text-lg mb-3">Naše kryty zvládnou pád, prach i dobrodružství díky precizní 3D tiskové technologii.</p>
                <p class="text-lg">Vyrobeny z odolných, ekologických materiálů s perfektním přizpůsobením pro váš telefon.</p>
            </div>
            <div>
                <img src="/uploads/home/sekera.jpg" alt="Odolnost krytu" class="section-img shadow-lg">
            </div>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center border-b border-[var(--color-accent)] pb-3 mb-6">Přizpůsobte si svůj kryt</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <img src="/uploads/home/krytka.jpg" alt="Krytka portu" class="section-img mb-3">
                <h3 class="text-xl font-semibold mb-2">Ochranná krytka portu</h3>
                <p>Chraňte port před prachem a nečistotami s odnímatelnou krytkou.</p>
            </div>
            <div class="p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <img src="/uploads/home/zaslepka.jpg" alt="Geometrický design" class="section-img mb-3">
                <h3 class="text-xl font-semibold mb-2">Clona přední kamery</h3>
                <p>Soukromí na prvním místě</p>
            </div>
            <div class="p-4 rounded-xl shadow-md hover:shadow-lg transition text-center">
                <img src="/uploads/home/pravitko.jpg" alt="Měřítko na krytu" class="section-img mb-3">
                <h3 class="text-xl font-semibold mb-2">Integrované měřítko</h3>
                <p>Praktické měřítko pro každodenní použití přímo na krytu.</p>
            </div>
        </div>
        <div class="text-center mt-8">
            <a href="';
		echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link('Home:default')) /* line 174 */;
		echo '" class="btn btn-accent shadow-md hover:shadow-lg">
                Začít navrhovat
            </a>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-semibold text-center border-b border-[var(--color-accent)] pb-3 mb-6">Galerie našich krytů</h2>
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="/uploads/home/pisek.png" alt="Kryt 1">
            </div>
            <div class="gallery-item">
                <img src="/uploads/home/showcase.jpg" alt="Kryt 2">
            </div>
            <div class="gallery-item">
                <img src="/uploads/home/showcase1.jpg" alt="Kryt 3">
            </div>
            <div class="gallery-item">
                <img src="/uploads/home/showcase2.jpg" alt="Kryt 4">
            </div>
        </div>
    </div>
</section>

<section class="contact">
    <div class="container mx-auto px-4">
        <h2 class="text-center">Kontakt</h2>
        <p class="text-center text-gray-600 mb-8">Máte otázky? Neváhejte nás kontaktovat.</p>
        <div class="flex flex-col md:flex-row md:space-x-8">
            <div class="contact-info mb-8 md:mb-0 flex-1">
                <p><i class="fas fa-user"></i> <strong>Martin Tkadlec</strong></p>
                <p><i class="fas fa-map-marker-alt"></i> <a href="#">Adresa: Sukova třída 1556, 530 02 Pardubice</a></p>
                <p><i class="fas fa-id-card"></i> <a href="#">IČO: 60919264</a></p>
                <p><i class="fas fa-phone"></i> <a href="tel:+737314477">Mobil: +737 314 477</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:autoskolaprima@email.cz">Email: autoskolaprima@email.cz</a></p>
            </div>
            <div class="md:w-1/2">
                <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="300" style="border:0; border-radius:8px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>


';
	}
}
