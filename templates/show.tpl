{if $draft}
    {if $currentPhase !== 'done' && $teamId && $teamId === $currentTurn && $draft.timeout > 0}
        <div id="initialTimeout">{$draft.timeout}</div>
    {/if}

    {if (!$accessKey || ($teamId && $teamId !== $currentTurn)) && $currentPhase !== 'done'}
        <meta http-equiv="refresh" content="30">
    {/if}

    <div class="info-container">
        <h4 class="text-center">
            {$translator->translate('action.show.draftHeading')|replace:"#1":$draft.teams[0].teamName|replace:"#2":$draft.teams[1].teamName nofilter}
        </h4>

        <hr>

        {if $currentPhase !== 'done' && $teamId}
            <div class="text-center turn-alert">
                {if $teamId === $currentTurn}
                    <div class="alert alert-success" role="alert">
                        {if $currentPhase === 'ban'}
                            {$translator->translate('action.show.yourTurnToBan')}
                        {else}
                            {$translator->translate('action.show.yourTurnToPick')}
                        {/if}

                        {if $draft.timeout > 0}
                            {capture assign=replacement}<span id="draftTimeoutTimer">{$draft.timeout}</span>{/capture}
                            {$translator->translate('action.show.timeLeft')|replace:'#1':$replacement nofilter}
                        {/if}
                    </div>
                {else}
                    <div class="alert alert-primary" role="alert">
                        {if $currentPhase === 'ban'}
                            {$translator->translate('action.show.enemyTurnToBan')}
                        {else}
                            {$translator->translate('action.show.enemyTurnToPick')}
                        {/if}
                    </div>
                {/if}
            </div>
        {/if}

        <div class="ban-grid">
            {foreach from=$draft.bannedTracks key=key item=track}
                <span class="img-container position-relative">
                    <img
                        src="{$router->getBaseUrl()}web/images/tracks/{$track.trackId}.jpg"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="rounded"
                    >

                    <span class="overlay position-absolute">X</span>
                </span>
            {/foreach}

            {if $draft.bannedTracks|count < ($draft.bans * 2)}
                {for $i=1 to ($draft.bans * 2 - count($draft.bannedTracks)) step 1}
                    <img
                        src="{$router->getBaseUrl()}web/images/spacer.png"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="placeholder rounded"
                    >
                {/for}
            {/if}
        </div>

        <div class="pick-grid">
            {foreach from=$draft.pickedTracks key=key item=track}
                <span class="img-container position-relative">
                    <img
                        src="{$router->getBaseUrl()}web/images/tracks/{$track.trackId}.jpg"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="rounded"
                    >

                    <span class="overlay position-absolute">{$track.name}</span>
                </span>
            {/foreach}

            {if $draft.pickedTracks|count < ($draft.picks * 2)}
                {for $i=1 to ($draft.picks * 2 - count($draft.pickedTracks)) step 1}
                    <img
                        src="{$router->getBaseUrl()}web/images/spacer.png"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="placeholder rounded"
                    >
                {/for}
            {/if}
        </div>
    </div>

    {if $tracks|count >= 1}
        <div class="track-selection-container text-center">
            <hr>

            <div class="form-group row">
                <label for="inputTrackSearch" class="col-sm-2 col-form-label">
                    {$translator->translate('action.show.searchTracksLabel')}
                </label>

                <div class="col-sm-2">
                    {if $draft.mode == 1}
                        <input type="text" class="form-control" id="inputTrackSearch" placeholder="{$translator->translate('action.show.searchTracksPlaceholderRace')}" autofocus>
                    {/if}

                    {if $draft.mode == 2}
                        <input type="text" class="form-control" id="inputTrackSearch" placeholder="{$translator->translate('action.show.searchTracksPlaceholderBattle')}" autofocus>
                    {/if}
                </div>
            </div>

            <div class="track-grid">
                {foreach from=$tracks key=index item=track}
                    {if $teamId && $teamId === $currentTurn && $track.isAvailable}
                        <form method="post" action="{$router->generateUrl('updateDraft')}" id="updateDraftForm{$track.id}" class="d-none">
                            <input type="text" name="teamId" value="{$teamId}">
                            <input type="text" name="accessKey" value="{$accessKey}">
                            <input type="text" name="draftId" value="{$draft.id}">
                            <input type="text" name="trackId" value="{$track.id}">
                        </form>
                    {/if}

                    <span{if !$track.isAvailable} class="track-unavailable"{/if} data-form-id="updateDraftForm{$track.id}"{if $teamId && $teamId === $currentTurn && $track.isAvailable} style="cursor: pointer"{/if}>
                        <span class="img-container position-relative" data-track="{$track.name}">
                            <img src="{$router->getBaseUrl()}web/images/tracks/{$track.id}.jpg" alt width="{$trackGridThumbnailSize}" class="img-thumbnail rounded">

                            <div class="overlay position-absolute">{$track.name}</div>
                        </span>
                    </span>
                {/foreach}
            </div>
        </div>
    {/if}
{else}
    <div class="container">
        <div class="alert alert-danger">{$translator->translate('action.show.draftDoesNotExist')|replace:'#1':$id}</div>
    </div>
{/if}