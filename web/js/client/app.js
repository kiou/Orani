$(function(){

    /* Menu déroulant */
    $('ul.sf-menu').superfish();

    $(document).on('click','ul.sf-menu .noLink', function(e){
        e.preventDefault();
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