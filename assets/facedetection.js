setInterval ( function () {
    $ ( '.face' ).remove ();
    $ ( '#video' ).faceDetection ( {
        interval : 1,
        complete : function ( faces ) {
            for ( var i = 0; i < faces.length; i++ ) {
                $ ( '<div>', {
                    'class' : 'face',
                    'css'   : {
                        'left'   : faces[ i ].x + 'px',
                        'top'    : faces[ i ].y + 'px',
                        'width'  : faces[ i ].width + 'px',
                        'height' : faces[ i ].height + 'px'
                    }
                } )
                    .insertAfter ( this );
            }
        },
        error    : function ( code, message ) {
            // alert ( 'Error: ' + message );
        }
    } );
}, 1000 );