$(function(){

    /* Tooltip */
    $(document).tooltip({
        track: true
    });

    /* Datepicker */
    if($('.form-date').length != 0){
        $('.form-date').datepicker($.datepicker.regional['fr']);
    }

    /* Timepicker */
    if($('.form-date-time').length != 0){
        $('.form-date-time').datetimepicker();
    }

    /* Scroll perso sur le menh à gauche */
    $('.menu').perfectScrollbar();

    /* Picto menu mobile */
    $('.headerMobile').on('click',function(){
        var bouton = $(this);
        var menu = $('.menu');
        var container = $('.container');

        if(!menu.hasClass('active')){
            bouton.addClass('active');
            menu.addClass('active');
            container.addClass('active');
        }else{
            bouton.removeClass('active');
            menu.removeClass('active');
            container.removeClass('active');
        }
    });

    /* DropDown */
    $('.dropDown').on('click',function(){
        var dropDown = $(this);
        var dropDownMenu = dropDown.find('.dropDownMenu');
        var dropDownHeight = dropDown.height();

        dropDownMenu.css({
            'top':dropDownHeight,
            'right': "0px"
        });

        if(!dropDown.hasClass('active')){
            dropDown.addClass('active');
            dropDownMenu.fadeIn('fast');
        }else{
            dropDown.removeClass('active');
            dropDownMenu.fadeOut('fast');
        }
    });

    /* Menu principal à gauche */
    $(".menu .menuNav").on('click',function(){

        var menuNav = $(this);
        var ul = menuNav.attr('data-nav');

        /* Si le menu n'est pas cliqué */
        if(menuNav.hasClass('active') == false){

            /* Reset */
            $('.menu .menuNav').removeClass('active');
            $('.menu ul:visible').slideUp('fast');

            /* Active */
            menuNav.addClass('active');
            $('ul.'+ul).slideDown('fast');

        }else{
            menuNav.removeClass('active');
            $('ul.'+ul).slideUp('fast');
        }
    });

    /* Publication dépublication */
    $(document).on('click','.tablePublucation',function(e){
        e.preventDefault();

        var td = $(this);
        var url = td.attr('data-url');
        td.html('<i class="fa fa-refresh loader fa-spin"></i>');

        $.ajax(url,{
            dataType : "JSON",
            cache:false
        })
        .done(function(data){
            if(data.state){
                td.html('<a href="#" title="Publication"><i class="tableAction turquoise fa fa-check"></i></a>');
            }else{
                td.html('<a href="#" title="Publication"><i class="tableAction rouge fa fa-check"></i></a>');
            }
        })
        .fail(function(){
            alert('Erreur Ajax');
        });
    });

    /* Supprimer un contenu avec une alerte sweet (sans ajax) */
    $(document).on('click','.sweetDelete',function(e){
        e.preventDefault();

        var lien = $(this);
        var url = lien.attr('href');
        var title = lien.attr('data-title');
        var text = lien.attr('data-text');

        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Annuler",
            confirmButtonText: "Supprimer",
            confirmButtonColor: "#00a99d",
            cancelButtonColor: '#e10e49',
            closeOnConfirm: false
        }).then(function () {
            window.top.location.href = url;
        });
    });

    /* Supprimer une image dans un contenu (avec ajax) */
    $(document).on('click','.sweetDeleteImage',function(e){

        e.preventDefault();

        var bouton = $(this);
        var image = bouton.attr('data-parent');
        var url = bouton.attr('href');

        swal({
            title: "êtes vous sur ?",
            text: "toute suppression est définitive",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Annuler",
            confirmButtonText: "Confirmer",
            confirmButtonColor: "#00a99d",
            cancelButtonColor: '#e10e49',
            closeOnConfirm: false
        }).then(function(){
            $.ajax(url)
            .done(function(data){
                swal("Supprimé !", "Image supprimée avec succès", "success");
                $('#'+image).fadeOut('fast');
            })
            .fail(function(){
                alert('Erreur ajax');
            });
        });

    });

    /* Alert simple sweet */
    $(document).on('click','.sweetAlert',function(e){
        e.preventDefault();

        var lien = $(this);
        var title = lien.attr('data-title');
        var text = lien.attr('data-text');

        swal(title, text, "warning");
    });

    /* Changement du poid d'un contenu */
    $('select[name="poid"]').change(function(){

        var select = $(this);
        var poid = select.val();
        var url = select.attr('data-url');
        var url = url+'/'+poid;

        select.after('<i class="managerLoader fa fa-refresh loader fa-spin"></i>');

        $.ajax(url)
        .done(function(data){
            $('.managerLoader').remove();
        })
        .fail(function(){
            alert('Erreur ajax');
        });

    });

    /* Administration du menu */
    if($('ol.sortableMenu').length != 0){

        $('ol.sortableMenu').nestedSortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div',
            revert: 250,
            maxLevels: 3,
            forcePlaceholderSize: true,
            placeholder: 'placeholder',
            excludeRoot: true,
            update:function(){

                var sortable = $(this);
                var results = sortable.nestedSortable('toArray');
                var url = sortable.attr('data-url');

                $.ajax(url,{
                    method:"POST",
                    data:{data:results}
                })
                .fail(function(){
                    alert('Erreur Ajax');
                });

            }
        });
    }

    /* Resize de la fenêtre du navigateur */
    $(window).resize(function() {

        var windowWidth = $(window).width();

        var bouton = $('.headerMobile');
        var menu = $('.menu');
        var container = $('.container');

        if(menu.hasClass('active')){
            bouton.removeClass('active');
            menu.removeClass('active');
            container.removeClass('active');
        }

    });

});