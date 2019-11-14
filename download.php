<?php
/**
 * User: Eduardo Kraus
 * Date: 16/06/2018
 * Time: 16:14
 */

ob_start();

require_once('../../config.php');
require_once(__DIR__ . "/classes/apis/server_proccess.php");

global $PAGE, $CFG, $OUTPUT;

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$zipfilename = required_param('zipfilename', PARAM_TEXT);

require_login();

$systemcontext = context_system::instance();

require_capability('moodle/site:config', $systemcontext);
require_capability('moodle/user:create', $systemcontext);
require_capability('moodle/user:update', $systemcontext);

$prefix = "curso-{$courseid}/usuario-{$userid}/";
$capturas = \local_facial\apis\aws\server_proccess::listCaptures($courseid, $userid);

if (isset($capturas['data'][0])) {

    $unique = uniqid(time());
    $fileZip = "{$CFG->tempdir}/{$unique}.zip";

    $zip = new ZipArchive();
    $zip->open($fileZip, ZipArchive::CREATE);

    foreach ($capturas['data'] as $captura) {

        $basename = pathinfo($captura['path'], PATHINFO_BASENAME);
        $urlImage = $captura['url'];
        $imageContent = file_get_contents($urlImage);
        $zip->addFromString($basename, $imageContent);
    }

    if ($zip->close()) {
        header('Content-Description: File Transfer');
        header('Content-type: application/zip');
        header("Content-disposition: attachment; filename=\"{$zipfilename}.zip\"");
        header('Expires: 0');
        header('Content-Length:' . filesize($fileZip));

        readfile($fileZip);
    } else {
        die ("Erro ao criar o ZIP!");
    }
} else {
    die ("Nenhuma captura localizada para este curso!");
}