{$translator->translate('action.index.welcomeText')}

<br><br>

<p>{$translator->translate('action.index.explanation')}</p>

<a href="{$router->generateUrl('new')}">
    <button class="btn btn-primary">
        <img src="{$router->getBaseUrl()}web/images/icons-white/play.svg" alt>
        {$translator->translate('action.index.getStartedButtonLabel')}
    </button>
</a>