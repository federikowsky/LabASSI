// scrivi codice jquery che usando toggleClass attivi una class css su .header se lo scroll verticale supera 50px e lo disattiva se lo scroll è inferiore a 50px 

$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.header').addClass('active');
        } else {
            $('.header').removeClass('active');
        }
    });
}