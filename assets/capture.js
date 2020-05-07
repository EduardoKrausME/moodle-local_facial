(function () {
    var width = 640;
    var height;

    var bt_capturar = 'Capturar foto e enviar';
    var streaming   = false;

    var video, canvas, contentarea, captureButton, playbutton, startarea, error = null;

    $ ( function () {
        video         = document.getElementById ( 'video' );
        canvas        = document.getElementById ( 'canvas' );
        contentarea   = $ ( '#contentarea' );
        captureButton = $ ( '#capture-button' );
        playbutton    = $ ( '#playbutton' );
        startarea     = $ ( '#start-area' );
        error         = $ ( '#error' );

        startarea.click ( playbutton_click );
        captureButton.click ( captureButton_click );

        video.addEventListener ( 'canplay', video_canplay, false );
    } );

    function video_canplay () {
        if ( !streaming ) {
            height = video.videoHeight / (video.videoWidth / width);

            if ( isNaN ( height ) ) {
                height = width / (4 / 3);
            }

            video.setAttribute ( 'width', width );
            video.setAttribute ( 'height', height );
            canvas.setAttribute ( 'width', width );
            canvas.setAttribute ( 'height', height );
            streaming = true;
        }
    }

    function playbutton_click () {
        showCamera ();

        try {
            captureButton.html ( bt_capturar ).prop ( "disabled", false );
            startarea.hide ();
            contentarea.show ();
        } catch ( err ) {
            console.log ( err );
        }
    }

    function captureButton_click () {
        event.preventDefault ();
        takepicture ();
    }

    function showCamera () {

        var mediaConfig = { video : true, audio : false };

        if ( navigator.mediaDevices && navigator.mediaDevices.getUserMedia ) {
            navigator.mediaDevices.getUserMedia ( mediaConfig )
                .then ( function ( stream ) {
                    try {
                        video.srcObject = stream;
                    } catch ( error ) {
                        video.src = window.URL.createObjectURL ( stream );
                    }

                    video.play ();

                    startarea.hide ();
                    contentarea.show ();
                    captureButton.html ( bt_capturar ).prop ( "disabled", false );
                }, function ( motivo ) {
                    console.log(motivo);
                    error.html ( "Navegador não possui câmera para acessar o reconhecimento!<br><br>" +
                        "Você precisa de um computador com WebCam.<br><br>" +
                        "Sistema não funciona em iPhone e iPad. Use o aplicativo!" )
                        .addClass ( 'error' );
                    $ ( ".video-container,#capture-button,#start-area" ).hide ();
                    contentarea.show ();

                    return false;
                } );
        } else {

            navigator.getMedia = (
                navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia);

            if ( typeof navigator.getMedia == "undefined" ) {
                console.log(navigator.getMedia);
                error.html ( "Navegador não possui suporte a reconhecimento!<br><br>" +
                    "Você precisa de um computador com WebCam.<br><br>" +
                    "Sistema não funciona em celulares ou tablets. Use o aplicativo!" )
                    .addClass ( 'error' );
                $ ( ".video-container,#capture-button,#start-area" ).hide ();
                contentarea.show ();

                return false;
            }

            navigator.getMedia ( mediaConfig,
                function ( stream ) {
                    if ( navigator.mozGetUserMedia ) {
                        video.mozSrcObject = stream;
                    } else {
                        var vendorURL = window.URL || window.webkitURL;
                        video.src     = vendorURL.createObjectURL ( stream );
                    }
                },
                function ( err ) {
                    console.log ( err );
                    error.html ( "Um erro ocorreu ao carregar a câmera:<br> " + err )
                        .addClass ( 'error' );
                    $ ( ".video-container,#capture-button,#start-area" ).hide ();
                    contentarea.show ();

                    return false;
                }
            );
        }
    }

    function takepicture () {
        var context = canvas.getContext ( '2d' );
        if ( width && height ) {
            canvas.width  = width;
            canvas.height = height;
            context.drawImage ( video, 0, 0, width, height );

            var data = canvas.toDataURL ( 'image/jpeg', 0.8 );

            error.html ( '' )
                .removeClass ( 'error' );
            captureButton.html ( 'Enviando imagem para o servidor...' )
                .prop ( "disabled", true );

            $.post ( 'fotossalvar.php', { courseid : courseid, imagem : data },
                function ( return_data ) {
                    console.log ( return_data );

                    if ( return_data.status == 'success' ) {
                        window.location = request_uri;
                    } else {
                        error.html ( return_data.message )
                            .addClass ( 'error' );
                        captureButton.html ( bt_capturar )
                            .prop ( "disabled", false );
                    }
                }, 'json' )
                .fail ( function ( error ) {
                    error.html ( "Erro ao enviar a imagem ao servidor. Capture novemente!" )
                        .addClass ( 'error' );
                    captureButton.html ( bt_capturar )
                        .prop ( "disabled", false );
                } );
        }
    }
}) ();



