let $ = require('jquery');

$(document).ready(function() {
    $('.unrevealed').click(function() {
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'GET',
            dataType: 'json',
            async: true,
            success: function(data) {
                let piece = JSON.parse(data.piece);
                let pieceBlock = $('[data-id="' + piece.id + '"]');

                pieceBlock.find('.count').html(piece.nbClickToReveal);

                let unrevealedBlock = pieceBlock.find('.unrevealed');
                if (piece.revealed && unrevealedBlock.length) {
                    unrevealedBlock.remove();
                    pieceBlock.append(
                        `<img src="${piece.pictureUrl}" alt="${
                            piece.filename
                        }">`
                    );
                }
            }
        });
    });

    $('.custom-file input').change(function(e) {
        $(this)
            .next('.custom-file-label')
            .html(e.target.files[0].name);
    });
});
