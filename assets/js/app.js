var $ = require('jquery');

$(document).ready(function() {
    $('.unrevealed').click( function(){
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            dataType: 'json',
            async: true
        })
    });
});