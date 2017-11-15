$(function(){

    /* Menu déroulant */
    $('ul.sf-menu').superfish();

    $(document).on('click','ul.sf-menu .noLink', function(e){
        e.preventDefault();
    });

});