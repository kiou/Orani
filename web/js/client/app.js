/**
 * Variables
 */
var pathNewsletter = document.querySelector('.newsletterWave path');
var fromNewsletter = pathNewsletter.getAttribute('d');
var toNewsletter = pathNewsletter.getAttribute('data-to');

/**
 * Functions
 */
/* Animation svg bloc newsletter */
function newsletterWave(hide){
    var direction = (hide != undefined) ? fromNewsletter  : toNewsletter ;

    dynamics.animate(pathNewsletter,{
        d:direction
    },{
        type: dynamics.easeInOut,
        duration: 600,
        friction: 100
    });
}

/**
 * Jquery
 */
$(function(){

    /* Header mobile */
    $(document).on('click','.headerBtnMobile',function(){
        $(this).addClass('active');

        $('.headerMobile').fadeIn(200);
    });

    /* Fermer header mobile */
    $(document).on('click','.headerMobile',function(e){
        if($(e.target).attr('class') == 'headerMobile'){
             $('.headerBtnMobile').removeClass('active');

             $(this).fadeOut(200);
        }
    });

        /* Hauteur responsive */
    if($('.matchHeight').length != 0){
        $('.matchHeight').matchHeight();
    }

    /* Menu collant */
    $(".header").sticky({topSpacing:0});

    /* Navigation via JS */
    $(document).on('click','.navFull',function(e){
        e.preventDefault();

        var elem = $(this);
        var lien = (elem.attr('href') != undefined) ? elem.attr('href') : elem.attr('data-url');
        window.location.href = lien;

    });

    /* Select 2 */
    $('.select2').select2({
        placeholder: function(){
            $(this).data('placeholder');
        }
    });

    /* Changement de langue */
    $('.headerSelecteur select').on('change',function(){
        top.location.href = $(this).val();
    });

    /* Menu déroulant */
    $('ul.sf-menu').superfish();

    /* menu déroulant */
    $(document).on('click','.navigation .noLink', function(e){
        e.preventDefault();
    });

    /* Afficher la newsletter */
    $(document).on('click', '.openNewsletter', function (e) {
        e.preventDefault();
        newsletterWave();
        $('.newsletter').addClass('active');
    });

    /* Fermer la newsletter */
    $(document).on('click', '.newsletterClose', function () {
        newsletterWave(true);
        $('.newsletter').removeClass('active');
    });

    /* Valider la newsletter */
    $(document).on('click','#newsletterForm button',function(e){
        e.preventDefault();
        var button = $(this);
        var url = $('#newsletterForm').attr('action');
        var html = '';

        if(!button.hasClass('current')){
            button.prepend('<i class="fa fa-refresh fa-spin"></i>');
            button.addClass('current');

            var fd = new FormData(document.getElementById("newsletterForm"));

            $.ajax(url,{
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                cache:false
            })
            .done(function(data){
                button.find('.fa').remove();
                button.removeClass('current');

                /* Supprimer le message si il éxiste déjà */
                if($('.message').length) $('.message').remove();

                /* Afficher le résultat */
                if(data.succes != undefined){
                    html = '<div class="message succes"><p>';
                        html += data.succes;
                    html += '</p></div>';

                    /* Reset des champs */
                    $('input[name="userbundle_newsletter[email]"]').val('');
                }
                else{
                    var label = Object.keys(data.error);

                    html = '<div class="message error"><p>';
                        for (var i = 0; i < label.length; i++) {
                            html += data.error[label[i]][0]+'<br>';
                        }
                    html += '</p></div>';
                }

                /* Afficher le contenu des messages */
                $(html).hide().prependTo($('#newsletterForm')).fadeIn();
            })
            .fail(function(){
                alert('Erreur ajax');
            });
        }
    });

    /* Partage sur les réseaux sociaux */
    $(document).on('click','.partage button',function(e){
        e.preventDefault();

        var button = $(this);
        var url = button.attr('data-url');
        var titre = button.attr('data-titre');

        var popupWidth = 640;
        var popupHeight = 320;
        var windowLeft = window.screenLeft || window.screenX;
        var windowWidth = window.innerWidth || document.documentElement.clientWidth;
        var popupLeft = windowLeft + windowWidth / 2 - popupWidth / 2 ;

        if(button.hasClass('twitter')){
           var shareUrl = 'https://twitter.com/intent/tweet?text='+ encodeURIComponent(titre)+'&url='+encodeURIComponent(url)
           var popupTitre = 'Partage sur twitter';
        }else if (button.hasClass('facebook')){
            var shareUrl = 'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(url)
            var popupTitre = 'Partage sur facebook';
        }else if (button.hasClass('linkedin')){
            var shareUrl = 'https://www.linkedin.com/shareArticle?url='+encodeURIComponent(url)
            var popupTitre = 'Partage sur Linkedin';
        }

        window.open(shareUrl, popupTitre, 'scrollbars=yes, width=' + popupWidth + ', height=' + popupHeight + ', top= 0' + ', left=' + popupLeft);

    });

});

$(window).on('load', function() {
    $('.newsletter').css({'display':'block'});
});