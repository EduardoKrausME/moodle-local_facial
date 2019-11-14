<?php
/**
 * User: Eduardo Kraus
 * Date: 30/01/17
 * Time: 08:34
 */

ob_start ();
require ( '../../config.php' );

global $PAGE, $CFG, $OUTPUT;

require_once $CFG->libdir . '/adminlib.php';

$courseid    = required_param ( 'courseid', PARAM_INT );
$request_uri = required_param ( 'request_uri', PARAM_TEXT );

$course = $DB->get_record ( 'course', array( 'id' => $courseid ), '*', MUST_EXIST );
require_login ( $course );

$PAGE->set_url ( new moodle_url( '/local/facial/detect.php' ) );
$PAGE->set_context ( context_course::instance ( $courseid ) );
$PAGE->set_pagetype ( 'admin-setting' );
// $PAGE->set_pagelayout ( 'base' );
$PAGE->set_title ( "Detecção de Face" );
$PAGE->set_heading ( "Detecção de Face" );

$PAGE->requires->jquery ();

?>
<!doctype html>
<html>
<head>
    <title>Detecção de Face</title>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="assets/main.css" type="text/css" media="all">
    <style>
        <?php echo get_config ( 'local_facial', 'cssextra'  ); ?>
    </style>
    <script src="assets/jquery-1.11.0.js"></script>
    <script>
        wwwroot     = "<?php echo $CFG->wwwroot; ?>";
        courseid    = "<?php echo $courseid; ?>";
        request_uri = "<?php echo $request_uri; ?>";
    </script>

    <?php
    // <script src="assets/jquery.facedetection.js"></script>
    // <script src="assets/facedetection.js"></script>
    ?>

    <script src="assets/capture.js"></script>
</head>
<body>
<div id="contentarea">
    <div class="video-container">
        <video id="video">Video stream not available.</video>
    </div>
    <div id="error"></div>
    <button id="capture-button" disabled>...</button>
    <canvas id="canvas">
    </canvas>

</div>

<div id="start-area">
    <div class="description">
        <?php
        $where       = array(
            'courseid' => $courseid,
            'userid'   => $USER->id
        );
        $principalId = $DB->get_field ( 'local_facial_principal', 'id', $where );
        if ( !$principalId ) {
            echo "<div class='principal'>";
            echo get_config ( 'local_facial', 'description_primeira' );
            echo "</div>";
        } else {
            echo get_config ( 'local_facial', 'description_course_' . $courseid );
        }
        ?>
    </div>
    <img src="pix/rosto.png" alt="Rosto">
    <button id="playbutton"><?php echo get_string ( 'start_cam', 'local_facial' ) ?></button>
</div>

</body>
</html>