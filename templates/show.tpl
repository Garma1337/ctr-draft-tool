{if $draft}
    {if (!$accessKey || ($teamId && $teamId !== $currentTurn)) && $currentPhase !== 'done'}
        <meta http-equiv="refresh" content="2">
    {/if}

    <div class="info-container">
        <h4 class="text-center">
            Draft of <strong>{$draft.teams[0].teamName}</strong> vs. <strong>{$draft.teams[1].teamName}</strong>
        </h4>
        <hr>

        {if $currentPhase !== 'done' && $teamId}
            <div class="text-center turn-alert">
                {if $teamId === $currentTurn}
                    It's your turn to {$currentPhase}!
                {else}
                    The enemy team is {if $currentPhase === 'ban'}banning{else}picking{/if}!
                {/if}
            </div>
        {/if}

        <div class="ban-grid">
            {foreach from=$draft.bannedTracks key=key item=track}
                {if $draft.teams[0].id === $track.teamId}
                    {$bannedByTeamA = true}
                {else}
                    {$bannedByTeamA = false}
                {/if}

                <span class="img-container position-relative">
                    <img
                        src="{$router->getBaseUrl()}images/tracks/{$track.trackId}.png"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="rounded{if $bannedByTeamA} banned-by-team-a{else} banned-by-team-b{/if}"
                    >

                    <span class="overlay position-absolute">X</span>
                </span>
            {/foreach}

            {if $draft.bannedTracks|count < ($draft.bans * 2)}
                {for $i=1 to ($draft.bans * 2 - count($draft.bannedTracks)) step 1}
                    <img
                        src="{$router->getBaseUrl()}images/spacer.png"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="placeholder rounded"
                    >
                {/for}
            {/if}
        </div>

        <div class="pick-grid">
            {foreach from=$draft.pickedTracks key=key item=track}
                {if $draft.teams[0].id === $track.teamId}
                    {$pickedByTeamA = true}
                {else}
                    {$pickedByTeamA = false}
                {/if}

                <span class="img-container position-relative">
                    <img
                        src="{$router->getBaseUrl()}images/tracks/{$track.trackId}.png"
                        alt
                        width="{$selectionThumbnailSize}"
                        class="rounded{if $pickedByTeamA} picked-by-team-a{else} picked-by-team-b{/if}"
                    >

                    <span class="overlay position-absolute">{$track.name}</span>
                </span>
            {/foreach}

            {if $draft.pickedTracks|count < ($draft.picks * 2)}
                {for $i=1 to ($draft.picks * 2 - count($draft.pickedTracks)) step 1}
                    <img
                        src="{$router->getBaseUrl()}images/spacer.png"
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
                <label for="inputTrackSearch" class="col-sm-2 col-form-label text-right">Search Tracks</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="inputTrackSearch" placeholder="i.e. Tiger Temple..." autofocus>
                </div>
            </div>

            <div class="track-grid">
                {foreach from=$tracks key=index item=track}
                    {if $teamId && $teamId === $currentTurn}
                        <form method="post" action="{$router->generateUrl('updateDraft')}" id="updateDraftForm{$track.id}" class="d-none">
                            <input type="text" name="teamId" value="{$teamId}">
                            <input type="text" name="accessKey" value="{$accessKey}">
                            <input type="text" name="draftId" value="{$draft.id}">
                            <input type="text" name="trackId" value="{$track.id}">
                        </form>
                    {/if}

                    <span data-form-id="updateDraftForm{$track.id}"{if $teamId && $teamId === $currentTurn} style="cursor: pointer"{/if}>
                        <span class="img-container position-relative" data-track="{$track.name}">
                            <img src="{$router->getBaseUrl()}images/tracks/{$track.id}.png" alt width="{$trackGridThumbnailSize}" class="img-thumbnail rounded">

                            <span class="overlay position-absolute">{$track.name}</span>
                        </span>
                    </span>
                {/foreach}
            </div>
        </div>
    {/if}
{else}
    <div class="container">
        <div class="alert alert-danger">There is no draft with the ID {$id}!</div>
    </div>
{/if}