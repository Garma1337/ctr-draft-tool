<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- External Libraries -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

        <title>CTR Draft Tool v2</title>

        <!-- Local Files --->
        {if $selectedTheme === $darkTheme}
            <link rel="stylesheet" href="{$router->getBaseUrl()}web/css/bootstrap-darkly.min.css">
        {/if}

        <link rel="stylesheet" href="{$router->getBaseUrl()}web/css/draft-tool.css">
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
                                <img src="{$router->getBaseUrl()}images/icons-white/plus.svg" alt>
                                {$translator->translate('action.index.navigationCreateDraft')}
                            </a>
                        </li>

                        <li class="nav-item{if $action === 'draftList'} active{/if}">
                            <a class="nav-link" href="{$router->generateUrl('draftList')}">
                                <img src="{$router->getBaseUrl()}images/icons-white/align-justify.svg" alt>
                                {$translator->translate('action.index.navigationDraftList')}
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarLanguageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{$router->getBaseUrl()}images/icons-white/flag.svg" alt>
                                {$translator->translate('action.index.navigationLanguage')}
                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                {foreach from=$translator->getLanguages() item=language}
                                    <a class="dropdown-item" href="{$router->generateUrl('index', ['language' => $language])}">{$language|ucfirst}</a>
                                {/foreach}
                            </div>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarLanguageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{$router->getBaseUrl()}images/icons-white/pen-tool.svg" alt>
                                Theme
                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{$router->generateUrl('index', ['theme' => 'light'])}">Light</a>
                                <a class="dropdown-item" href="{$router->generateUrl('index', ['theme' => 'dark'])}">Dark</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container main-container main-body action-{$action}">
            <div id="currentLanguage" class="d-none">{$translator->getCurrentLanguage}</div>

            {$content nofilter}
        </div>
    </body>
</html>