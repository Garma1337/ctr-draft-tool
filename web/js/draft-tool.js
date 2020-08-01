/* taken from https://j11y.io/javascript/regex-selector-for-jquery/ */
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

/* main Draft Tool JS functions */
$(document).ready(function() {
    new ClipboardJS('.btn');

    var draftCreateForm = $('#draftCrateForm');
    var draftCreateFormSubmitButton = $('#draftCrateForm button[type="submit"]');

    /* Submit a draft */
    draftCreateFormSubmitButton.on('click', function(e) {
        e.preventDefault();

        var alertContainer = $('.alerts');
        alertContainer.empty();

        $.post(
            draftCreateForm.attr('action'),
            {
                'teamA'                 : $('#inputTeamA').val(),
                'teamB'                 : $('#inputTeamB').val(),
                'bans'                  : $('#inputNumberBans').val(),
                'picks'                 : $('#inputNumberPicks').val(),
                'timeout'               : $('#inputTimeout').val(),
                'enableSpyroCircuit'    : Number($('#enableSpyroCircuit').is(':checked')),
                'enableHyperSpaceway'   : Number($('#enableHyperSpaceway').is(':checked')),
                'enableRetroStadium'    : Number($('#enableRetroStadium').is(':checked')),
                'splitTurboRetro'       : Number($('#splitTurboRetro').is(':checked')),
                'allowTrackRepeats'     : Number($('#allowTrackRepeats').is(':checked'))
            }
        ).done(function(data) {
            if (data.length > 0) {
                var messages = JSON.parse(data);
                var errors = messages.errors;
                var success = messages.success;
                var draftData = messages.draftData;

                // this is super not clean but eh ... whatever
                for (var key in errors) {
                    alertContainer.append(
                        '<div class="alert alert-danger" role="alert">'
                        + errors[key] +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                        + '<span aria-hidden="true">&times;</span>'
                        + '</button>'
                        + '</div>'
                    );
                }

                if (success !== undefined) {
                    var baseUrl = $('#baseUrl').text();
                    var spectatorUrl = baseUrl + 'index.php?action=show&id=' + draftData.id;
                    var teamAUrl = baseUrl + 'index.php?action=show&id=' + draftData.id + '&accessKey=' + draftData.accessKeyA;
                    var teamBUrl = baseUrl + 'index.php?action=show&id=' + draftData.id + '&accessKey=' + draftData.accessKeyB;

                    $('#inputSpectatorUrl').attr('value', spectatorUrl);
                    $('#inputTeamAUrl').attr('value', teamAUrl);
                    $('#inputTeamBUrl').attr('value', teamBUrl);

                    $('#inputSpectatorUrl + div > a').attr('href', spectatorUrl);
                    $('#inputTeamAUrl + div > a').attr('href', teamAUrl);
                    $('#inputTeamBUrl + div > a').attr('href', teamBUrl);

                    $('.action-new .draft-create-success').removeClass('d-none');

                    /* Disable form to prevent people from creating lobbies in fast succession */
                    draftCreateForm.attr('action', '#');
                    draftCreateFormSubmitButton.attr('disabled', 'disabled')
                }
            }
        });
    });

    /* Show the "Split Turbo Track and Retro Stadium" option when Retro Stadium is enabled */
    $('#enableRetroStadium').on('change', function() {
        if ($(this).is(':checked')) {
            $('#splitTurboRetro').parent('div').removeClass('d-none');
        } else {
            $('#splitTurboRetro').parent('div').addClass('d-none');
        }
    });

    /* Draft Timeout */
    var initialTimeout = $('#initialTimeout');
    if (initialTimeout.length) {
        var timeLeft = localStorage.getItem('draft.timeLeft');
        if (timeLeft === null) {
            timeLeft = initialTimeout.text();
        }

        window.setInterval(function () {
            timeLeft--;
            localStorage.setItem('draft.timeLeft', timeLeft);
            $('#draftTimeoutTimer').text(timeLeft);

            if (timeLeft <= 0) {
                localStorage.removeItem('draft.timeLeft');

                /* Select random track if timeout was reached */
                $('form#updateDraftForm0').submit();
            }
        }, 1000);
    }

    /* Color the border of the placeholders */
    $('.ban-grid img').each(function(index) {
        if (index % 2 === 0) {
            $(this).addClass('banned-by-team-a').removeClass('placeholder');
        } else {
            $(this).addClass('banned-by-team-b').removeClass('placeholder');
        }
    });

    $('.pick-grid img').each(function(index) {
        if ((index + 1) % 4 === 0 || (index + 1) % 4 === 1) {
            $(this).addClass('picked-by-team-a').removeClass('placeholder');
        } else {
            $(this).addClass('picked-by-team-b').removeClass('placeholder');
        }
    });

    /* Search Tracks in the Track Grid */
    $('#inputTrackSearch').on('input', function() {
        var name = $(this).val();

        if (name.length <= 0) {
            $('.track-grid .img-container').show();
        } else {
            $('.track-grid .img-container').hide();
            $(':regex(data-track, ' + name + ')').show();
        }
    });

    /* Submit a track selection */
    $('.track-grid > span').on('click', function() {
        var formId = $(this).data('form-id');

        $('form#' + formId).submit();
    });
});