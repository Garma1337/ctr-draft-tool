{if $drafts|count > 0}
    {include file='_draftListPagination.tpl'}

    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Team A</th>
                <th scope="col">Team B</th>
                <th scope="col">{$translator->translate('action.draftList.tableHeadNumberBans')}</th>
                <th scope="col">{$translator->translate('action.draftList.tableHeadNumberPicks')}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$drafts item=draft}
                <tr>
                    <td>{$draft.id}</td>
                    <td>{$draft.teams[0].teamName}</td>
                    <td>{$draft.teams[1].teamName}</td>
                    <td>{$draft.bans}</td>
                    <td>{$draft.picks}</td>
                    <td>
                        <a href="{$router->generateUrl('show', ['id' => $draft.id])}">
                            <button class="btn btn-primary">
                                <img src="{$router->getBaseUrl()}images/icons-white/zoom-in.svg" alt>
                                {$translator->translate('action.draftList.buttonShowLabel')}
                            </button>
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    {include file='_draftListPagination.tpl'}
{else}
    <div class="alert alert-primary">There are currently no drafts. Why don't you <a href="{$router->generateUrl('new')}">create one</a>?</div>
{/if}

