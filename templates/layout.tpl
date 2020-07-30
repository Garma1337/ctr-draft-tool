<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="{$router->getBaseUrl()}web/css/bootstrap.min.css">
        <link rel="stylesheet" href="{$router->getBaseUrl()}web/css/draft-tool.css">

        <title>CTR Draft Tool v2</title>

        <script src="{$router->getBaseUrl()}web/js/jquery-3.5.1.min.js"></script>
        <script src="{$router->getBaseUrl()}web/js/clipboard.min.js"></script>
        <script src="{$router->getBaseUrl()}web/js/bootstrap.min.js"></script>
        <script src="{$router->getBaseUrl()}web/js/draft-tool.js"></script>
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-xl main-container">
                <a class="navbar-brand" href="{$router->generateUrl('index')}">
                    <img src="{$router->getBaseUrl()}images/icons-white/grid.svg" alt> CTR Draft Tool v2
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item{if $action === 'new'} active{/if}">
                            <a class="nav-link" href="{$router->generateUrl('new')}">
                                <img src="{$router->getBaseUrl()}images/icons-white/plus.svg" alt> Create Draft
                            </a>
                        </li>
                        <li class="nav-item{if $action === 'draftList'} active{/if}">
                            <a class="nav-link" href="{$router->generateUrl('draftList')}">
                                <img src="{$router->getBaseUrl()}images/icons-white/align-justify.svg" alt> Previous Drafts
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container main-container main-body action-{$action}">
            {$content nofilter}
        </div>
    </body>
</html>