$(document).ready(function() {
    var settings = localStorage.getItem('settings');
    if (!settings) {
        var xApiKeyHeader = $('#x-api-key').val();
        var xApiClientHeader = $('#x-api-client').val();
        var authorizationHeader = $('#authorization').val();

        settings = {
            'X-API-KEY': xApiKeyHeader,
            'X-API-CLIENT': xApiClientHeader,
            'Authorization': authorizationHeader
        };

        localStorage.setItem('settings', JSON.stringify(settings));
    } else {
        settings = JSON.parse(settings);

        $('#x-api-key').val(settings['X-API-KEY']);
        $('#x-api-client').val(settings['X-API-CLIENT']);
        $('#authorization').val(settings['Authorization']);
    }

    var jsonEditors = {};

	$('#doc-menu').affix({
        offset: {
            top: ($('#header').outerHeight(true) + $('#doc-header').outerHeight(true)) + 45,
            bottom: ($('#footer').outerHeight(true) + $('#promo-block').outerHeight(true)) + 75
        }
    });
    
    /* Hack related to: https://github.com/twbs/bootstrap/issues/10236 */
    $(window).on('load resize', function() {
        $(window).trigger('scroll'); 
    });

    /* Activate scrollspy menu */
    $('body').scrollspy({target: '#doc-nav', offset: 100});
    
    /* Smooth scrolling */
	$('a.scrollto').on('click', function(e){
        //store hash
        var target = this.hash;    
        e.preventDefault();
		$('body').scrollTo(target, 800, {offset: 0, 'axis':'y'});
		
	});

	/* Settings modal */
	$('#save-settings').click(function(e){
        var xApiKeyHeader = $('#x-api-key').val();
        var xApiClientHeader = $('#x-api-client').val();
        var authorizationHeader = $('#authorization').val();

        var settings = {
            'X-API-KEY': xApiKeyHeader,
            'X-API-CLIENT': xApiClientHeader,
            'Authorization': authorizationHeader
        };

        localStorage.setItem('settings', JSON.stringify(settings));
    });

	/* Json editors init */
    $('.jsoneditor').each(function(){
        var container = document.getElementById($(this).attr('id'));
        var key = $(this).data('jsoneditor-key');
        var json = $(this).text();

        $(this).text('');

        var options = {
            modes: ['text'],
            mode: 'text',
            search: false
        };
        jsonEditors[key] = new JSONEditor(container, options, JSON.parse(json));
    });

    /* Forms with example request */
    $('[data-on=sample-request-form]').submit(function(e) {
        e.preventDefault();

        var editorId = $(this).attr('data-editor-id');
        var editor = jsonEditors[editorId];
        var param = editor.get();
        var $response = $('#response-' + editorId);

        var headers = localStorage.getItem('settings');
        if (headers) {
            headers = JSON.parse(headers);
        }

        var url = $('#api-url').val();

        var ajaxRequest = {
            url: url,
            headers: headers,
            data: JSON.stringify(param),
            dataType: 'json',
            contentType: 'application/json',
            type: 'POST',
            success: displaySuccess,
            error: displayError
        };

        $.ajax(ajaxRequest);

        function displaySuccess(data, status, jqXHR) {
            var jsonResponse;
            try {
                jsonResponse = JSON.parse(jqXHR.responseText);
                jsonResponse = JSON.stringify(jsonResponse, null, 4);
            } catch (e) {
                jsonResponse = data;
            }
            if (jqXHR.status === "204") {
                jsonResponse = "HTTP/1.1 204 OK";
            }

            $response.find('code').text(jsonResponse);
            $response.collapse('show');
        }

        function displayError(jqXHR, status, error) {
            var message = "Error " + jqXHR.status + ": " + error;
            var jsonResponse;
            try {
                jsonResponse = JSON.parse(jqXHR.responseText);
                jsonResponse = JSON.stringify(jsonResponse, null, 4);
            } catch (e) {
                jsonResponse = escape(jqXHR.responseText);
            }

            if (jsonResponse) {
                message += "<br>" + jsonResponse;
            }

            $response.find('code').text(message);
            $response.collapse('show');
        }
    });
});