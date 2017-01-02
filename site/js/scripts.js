jQuery(function() {
    jQuery('.tooltip').tooltipster({
        offsetY: 2
    });

    jQuery('.messages').first().append('<a class="close fa fa-times-circle"></a>');
    jQuery('.messages .close').click(function() {
        jQuery('.messages').fadeOut('fast');
    });

});