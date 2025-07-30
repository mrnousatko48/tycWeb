<?php
declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        // Admin modul (unchanged)
        $adminRouter = new RouteList('Admin');
        $adminRouter->addRoute('admin/<presenter>/<action>[/<id>]', 'Dashboard:default');
        $router->add($adminRouter);

        // Front modul with language prefix
        $frontRouter = new RouteList('Front');
        $frontRouter->addRoute('<lang cs|en>/<presenter>/<action>[/<id>]', [
            'presenter' => 'Home',
            'action' => 'default',
            'lang' => 'cs', // Default to Czech
        ]);
        $router->add($frontRouter);

        return $router;
    }
}