$(function () {
    var el = document.querySelectorAll('.user-actions');
    for (var i = 0; i < el.length; i++) {
        el[i].addEventListener('mouseover', function () {
            this.parentNode.style.backgroundColor = '#222';
        });
        el[i].addEventListener('mouseleave', function () {
            this.parentNode.style.backgroundColor = '#252422';
        });
    }
    $('.toggleModal').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $('#modalAnswerComment').modal();
        $('#modal__load-answerComment').html('Chargement...');
        $.ajax({
            url: url,
            method: 'GET'
        }).done(function (msg) {
            $('#modal__load-answerComment').html(msg);
            $('#answerComment').attr('formaction', url);
        });
    });
});
