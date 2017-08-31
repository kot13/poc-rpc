$(document).ready(function() {
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
});