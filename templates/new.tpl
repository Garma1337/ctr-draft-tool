<div class="draft-create-success d-none">
    <div class="alert alert-success">Your Draft was created successfully!</div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Spectator Link</label>
        <div class="col-sm-10">
            <div class="input-group mb-3">
                <input type="text" class="form-control col-md-10" readonly value="test" id="inputSpectatorUrl">

                <div class="input-group-append">
                    <a href="" target="_blank">
                        <button type="button" class="btn btn-outline-secondary"><img src="{$router->getBaseUrl()}images/icons-black/external-link.svg" alt> Open</button>
                    </a>

                    <button type="button" class="btn btn-outline-secondary" data-clipboard-target="#inputSpectatorUrl"><img src="{$router->getBaseUrl()}images/icons-black/copy.svg" alt> Copy</button>
                </div>
            </div>
        </div>

        <label class="col-sm-2 col-form-label">Link Team A</label>
        <div class="col-sm-10">
            <div class="input-group mb-3">
                <input type="text" class="form-control col-md-10" readonly value="" id="inputTeamAUrl">

                <div class="input-group-append">
                    <a href="" target="_blank">
                        <button type="button" class="btn btn-outline-secondary"><img src="{$router->getBaseUrl()}images/icons-black/external-link.svg" alt> Open</button>
                    </a>

                    <button type="button" class="btn btn-outline-secondary" data-clipboard-target="#inputTeamAUrl"><img src="{$router->getBaseUrl()}images/icons-black/copy.svg" alt> Copy</button>
                </div>
            </div>
        </div>

        <label class="col-sm-2 col-form-label">Link Team B</label>
        <div class="col-sm-10">
            <div class="input-group mb-3">
                <input type="text" class="form-control col-md-10" readonly value="" id="inputTeamBUrl">

                <div class="input-group-append">
                    <a href="" target="_blank">
                        <button type="button" class="btn btn-outline-secondary"><img src="{$router->getBaseUrl()}images/icons-black/external-link.svg" alt> Open</button>
                    </a>

                    <button type="button" class="btn btn-outline-secondary" data-clipboard-target="#inputTeamBUrl"><img src="{$router->getBaseUrl()}images/icons-black/copy.svg" alt> Copy</button>
                </div>
            </div>
        </div>
    </div>

    <hr>
</div>


<div id="baseUrl" class="d-none">{$baseUrl}</div>
<div class="alerts"></div>

<form action="{$formAction}" method="post">
    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="inputTeamA">Team A</label>
            <input type="text" class="form-control" id="inputTeamA" name="teamA">
        </div>

        <div class="form-group col-md-2">
            <label for="inputTeamB">Team B</label>
            <input type="text" class="form-control" id="inputTeamB" name="teamB">
        </div>
    </div>

    <div class="form-group">
        <label for="inputNumberBans"># of bans per Team</label>
        <input type="number" class="form-control col-md-1" id="inputNumberBans" name="bans" value="3" min="0" max="17">
    </div>

    <div class="form-group">
        <label for="inputNumberPicks"># of picks per Team</label>
        <input type="number" class="form-control col-md-1" id="inputNumberPicks" name="picks" value="5" min="1" max="30">
    </div>

    <div class="form-group">
        <label for="inputTimeout">Timeout for banning / picking (leave empty for no timeout)</label>
        <input type="number" class="form-control col-md-1" id="inputTimeout" name="timeout" min="15" max="60">
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="enableSpyroCircuit" id="enableSpyroCircuit">
        <label class="form-check-label" for="enableSpyroCircuit">
            Enable Spyro Circuit
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="enableHyperSpaceway" id="enableHyperSpaceway">
        <label class="form-check-label" for="enableHyperSpaceway">
            Enable Hyper Spaceway
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="enableRetroStadium" id="enableRetroStadium">
        <label class="form-check-label" for="enableRetroStadium">
            Enable Retro Stadium
        </label>
    </div>

    <div class="form-check d-none">
        <input class="form-check-input" type="checkbox" name="splitTurboRetro" id="splitTurboRetro">
        <label class="form-check-label" for="splitTurboRetro">
            Split Turbo Track and Retro Stadium
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="allowTrackRepeats" id="allowTrackRepeats">
        <label class="form-check-label" for="allowTrackRepeats">
            Allow a track to be picked more than once
        </label>
    </div>

    <div class="form-group row">
        <div class="col-sm-3">
            <button type="submit" class="btn btn-primary btn-lg submit-button">
                <img src="images/icons-white/check.svg" width="24"> Submit
            </button>
        </div>
    </div>
</form>