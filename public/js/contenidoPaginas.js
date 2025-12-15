$('document').ready(function () {

    $('.contenidoPaginasAjax').on('click', function (e) {
        e.preventDefault();
        direccion = $(this).attr("href");
        $.ajax({
            url: direccion,
            success: function (result) {
                $("#contenido").html(result);
            }}); // fin del ajax
    }); // fin del click


}); // fin del ready









