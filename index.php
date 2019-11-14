<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local_facial
 * @copyright  2018 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require __DIR__ . "/classes/util/message_status.php";
require_once __DIR__ . "/classes/apis/server_proccess.php";

global $PAGE, $CFG, $OUTPUT;

$userid = required_param('userid', PARAM_INT);
$courseid = optional_param('courseid', 1, PARAM_INT);
$file = optional_param('file', false, PARAM_TEXT);
$info = optional_param('info', false, PARAM_RAW);
$title = optional_param('title', false, PARAM_TEXT);
$resetar = optional_param('resetar', false, PARAM_TEXT);

$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

require_login();

$systemcontext = context_system::instance();

require_capability('moodle/user:create', $systemcontext);
require_capability('moodle/user:update', $systemcontext);

$PAGE->set_url(new moodle_url('/local/facial/', array('userid' => $userid)));
$PAGE->set_context($systemcontext);
$PAGE->set_pagetype('admin-setting');
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Lista capturas');
$PAGE->set_heading('Lista capturas');

echo $OUTPUT->header();


if ($file) {
    echo "<p><a href='?userid={$userid}&courseid={$courseid}'>Voltar a lista de imagens</a></p>";

    $basename = pathinfo($file, PATHINFO_BASENAME);
    echo $OUTPUT->heading('Captura  <strong>' . $title . "</strong> de " . fullname($user));

    echo "<p>Clique na imagem para baixar</p>";
    echo "<p><a href='{$file}' target='_blank'><img src='{$file}'></a></p>";

    echo $OUTPUT->heading('Logs da Captura', 3);
    echo $info;

} else {

    echo $OUTPUT->heading('Lista capturas de ' . fullname($user));

    if ($courseid < 2) {
        $courses = enrol_get_users_courses($userid, true);
    } else {
        echo "<p><a href='?userid={$userid}&courseid=1'>Ver todos os cursos</a></p>";
        $courses[0] = $DB->get_record('course', array('id' => $courseid), 'id,fullname', MUST_EXIST);;
    }

    if (count($courses) == 0) {
        \local_facial\util\message_status::print_info(
            "Aluno sem nenhuma matrícula!");
    }

    foreach ($courses as $course) {
        if ($courseid < 2) {
            $textLink = "<a href='?userid={$userid}&courseid={$course->id}'>{$course->fullname}</a>";
            echo $OUTPUT->heading($textLink, 3);
        } else {
            echo $OUTPUT->heading($course->fullname, 3);
        }

        $zipFilename = urlencode("{$user->firstname} {$user->lastname}");
        echo "<p><a target='_blank' href='download.php?userid={$userid}&courseid={$course->id}&zipfilename={$zipFilename}'>Baixar todas as imagens e LOGS em ZIP</a></p>";


        $capturaPrincipal = $DB->get_field('local_facial_principal', 'captura',
            array('courseid' => $course->id,
                'userid' => $user->id
            ));
        if ($resetar && $resetar == $capturaPrincipal) {
            $DB->delete_records('local_facial_principal',
                array(
                    'courseid' => $course->id,
                    'userid' => $user->id,
                    // 'captura'  => $resetar
                ));
            \local_facial\util\message_status::print_success(
                "Captura removida como principal. Da próxima vês ele terá que capturar a principal.");

            $capturaPrincipal = "";
        }

        $capturas = \local_facial\apis\aws\server_proccess::listCaptures($course->id, $userid);

        if (isset($capturas['data'][0])) {
            echo "<table cellspacing=\"0\" class=\"flexible reportlog generaltable generalbox\">
                      <thead>
                          <tr>
                              <th>Thumb</th>
                              <th>Arquivo <br> Data <br> Tamanho</th>
                              <th>Log</th>
                          </tr>
                      </thead>
                  <tbody>";

            foreach ($capturas['data'] as $captura) {

                if (strpos($captura['info'][0]['text'], "{") === false) {
                    $logText = $captura['info'][0]['text'];
                } else {
                    $info = json_decode($captura['info'][0]['text'], true);
                    $logText = '';
                    // $logText .= "<strong>Objetos encontrados</strong>: " . $info['objetos'] . "<br>";
                    $logText .= "<strong>Faces</strong>: " . $info['faces']['confidenceText'] . "<br>";
                    $logText .= "<strong>Comparação</strong>: " . $info['comparacao']['similarityText'] . "<br>";
                }

                $basename = pathinfo($captura['path'], PATHINFO_BASENAME);
                $urlInfo = "?userid={$userid}&courseid={$course->id}" .
                    "&title=" . $basename .
                    "&file=" . urlencode($captura['url']) .
                    "&info=" . urlencode($logText);

                $status = $corStatus = '';
                if ($capturaPrincipal && $captura['default'] == 'sim') {
                    $status = "<strong style='color:#ff0b37'>Imagem principal</strong><br>";
                    $corStatus = 'background: #90CAF9';
                }

                echo "<tr style='{$corStatus}'>
                <td style='vertical-align:middle;'><a href='{$urlInfo}' target='_blank'><img height='80' src='{$captura['url']}'></a></td>
                <td style='vertical-align:middle;white-space:nowrap'>
                        {$status}<a href='{$urlInfo}' target='_blank'>{$basename}</a><br>
                        {$captura['data']}</td>
                <td style='vertical-align:middle;'>{$logText}</td>
            </tr>";
            }
            echo "</tbody></table>";

            echo "<p><a href='?userid={$userid}&courseid={$course->id}&resetar={$capturaPrincipal}'>Resetar a imagem principal</a></p>";
        } else {
            \local_facial\util\message_status::print_info("Nenhuma captura localizada para este curso!");
        }
    }
}

echo $OUTPUT->footer();



