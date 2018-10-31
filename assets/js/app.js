var $ = require('jquery');

$(document).ready(function() {
    $('.unrevealed').click( function(){
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'GET',
            dataType: 'json',
            async: true,
            success : function(data){
                var piece = JSON.parse(data.piece);
                var pieceBlock = $('[data-id="' + piece.id + '"]');

                pieceBlock.find('.count').html(piece.nbClickToReveal);

                if (piece.revealed) {
                    pieceBlock.removeClass('unrevealed');
                }
            }
        })
    });
});