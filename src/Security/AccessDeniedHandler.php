<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

final class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $page = '<html>
                            <title>Plantify</title>
                            <link rel="preconnect" href="https://fonts.gstatic.com">
                            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
                            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                            <link rel="stylesheet" href="../../css/style.css">
                        <body>
                            <div class="nav-bar">
                                <div>
                                    <a class="nav-bar-item" href="/garden">К участку</a>
                                </div>
                                <div>
                                    <a class="nav-bar-item" href="/planning">Планировать новые посадки</a>
                                </div>
                                <div>
                                    <a class="nav-bar-item" href="/todo">To-do лист</a>
                                </div>
                            </div>
                        </body>
                    </html>';

        return new Response($page, 403);
    }
}
