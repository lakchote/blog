(function() {
    var el = document.querySelectorAll('.user-actions');
    for (var i = 0; i < el.length; i++) {
        el[i].addEventListener('mouseover', function () {
            this.parentNode.style.backgroundColor = '#222';
        });
        el[i].addEventListener('mouseleave', function () {
            this.parentNode.style.backgroundColor = '#252422';
        });
    }
}());
