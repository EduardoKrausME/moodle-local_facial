if ( document.getElementById ( 'id_s_local_facial_cssextra' ) ) {
    selectFacialChangue ();

    jQuery ( '.settingsform select' ).change ( selectFacialChangue );
}

function selectFacialChangue () {
    var itens = jQuery ( '.settingsform select' );

    jQuery.each ( itens, function ( id, item ) {

        var element = jQuery ( item );

        var idd            = element.attr ( 'id' );
        var redirect_id    = idd.replace ( "id_s_local_facial_enable_course_", "id_s_local_facial_redirect_course_" );
        var tempo_id       = idd.replace ( "id_s_local_facial_enable_course_", "id_s_local_facial_tempo_course_" );
        var description_id = idd.replace ( "id_s_local_facial_enable_course_", "id_s_local_facial_description_course_" );

        if ( element.val () == 'nao' ) {
            jQuery ( "#" + redirect_id ).parent ().parent ().parent ().hide ();
            jQuery ( "#" + tempo_id ).parent ().parent ().parent ().hide ();
            jQuery ( "#" + description_id ).parent ().parent ().parent ().hide ();
        } else {
            jQuery ( "#" + redirect_id ).parent ().parent ().parent ().show ();
            jQuery ( "#" + tempo_id ).parent ().parent ().parent ().show ();
            jQuery ( "#" + description_id ).parent ().parent ().parent ().show ();
        }
    } );
}