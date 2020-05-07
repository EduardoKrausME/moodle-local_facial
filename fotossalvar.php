<?php

ob_start();
require_once('../../config.php');
require_once(__DIR__ . "/classes/apis/server_proccess.php");

$courseid = optional_param('courseid', false, PARAM_TEXT);

$data = date('Y-m-d-H-i-s');
$extensao = "";
$imagem = optional_param('imagem', false, PARAM_RAW);
if (strpos($imagem, ":image/png;")) {
    $imagem = str_replace('data:image/png;base64,', '', $imagem);
    $extensao = "png";
} elseif (strpos($imagem, ":image/jpeg;")) {
    $imagem = str_replace('data:image/jpeg;base64,', '', $imagem);
    $extensao = "jpg";
} else {
    sendErrorLeft(get_string('error_imageminvalida', 'local_facial'));
}
$imagem = base64_decode($imagem);

$imageFile = $CFG->dataroot . '/temp/' . uniqid() . "." . $extensao;
file_put_contents($imageFile, $imagem);

if ($extensao == "jpg") {
    $im = imagecreatefromjpeg($imageFile);
} else {
    $im = imagecreatefrompng($imageFile);
}

$im = imagecreatefromstring($imagem);
if ($im == false) {
    sendErrorLeft(get_string('error_imageminvalida', 'local_facial'));
}

$proccess = \local_facial\apis\server_proccess::sendCapture($imageFile, $courseid, $USER->id);
unlink($imageFile);

if ($proccess['status'] == 'error') {
    sendError($proccess['error']);
} else {

    if ($proccess['default']) {
        $DB->delete_records('local_facial_principal',
            array(
                'courseid' => $course->id,
                'userid' => $user->id,
            ));

        $facial_principal = (object)[
            'courseid' => $course->id,
            'userid' => $user->id,
            'captura', $proccess['path'],
            'datetime' => time(),
        ];
        $DB->insert_record("local_facial_principal");
    }

    sendSuccess();
}

function sendError($message) {
    ob_clean();
    die(json_encode(array('status' => 'error', 'message' => $message)));
}

function sendErrorLeft($message) {
    ob_clean();
    sendError("<div style='text-align:left'>{$message}</div>");
    die();
}

function sendSuccess() {
    global $courseid;

    ob_clean();
    $_SESSION['LOCAL_FACIAL_' . $courseid] = time();
    die(json_encode(array('status' => 'success')));
}

