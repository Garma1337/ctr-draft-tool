// taken from https://j11y.io/javascript/regex-selector-for-jquery/
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

// main draft tool js functions
$(document).ready(function() {
    new ClipboardJS('.btn');

    $('#enableRetroStadium').on('change', function() {
        if ($(this).is(':checked')) {
            $('#splitTurboRetro').parent('div').removeClass('d-none');
        } else {
            $('#splitTurboRetro').parent('div').addClass('d-none');
        }
    });

    $('#inputTrackSearch').on('input', function() {
        var name = $(this).val();

        if (name.length <= 0) {
            $('.track-grid .img-container').show();
        } else {
            $('.track-grid .img-container').hide();
            $(':regex(data-track, ' + name + ')').show();
        }
    })

    $('button[type="submit"]').on('click', function(e) {
        e.preventDefault();

        var alertContainer = $('.alerts');
        alertContainer.empty();

        $.post(
            $('form').attr('action'),
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

                    $('#inputSpectatorUrl + a').attr('href', spectatorUrl);
                    $('#inputTeamAUrl + a').attr('href', teamAUrl);
                    $('#inputTeamBUrl + a').attr('href', teamBUrl);

                    $('.action-new .draft-create-success').removeClass('d-none');
                }
            }
        });
    });

    $('.track-grid > span').on('click', function() {
        var formId = $(this).data('form-id');

        $('form#' + formId).submit();
    });
});