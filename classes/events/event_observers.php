<?php
/**
 * User: Eduardo Kraus
 * Date: 26/10/17
 * Time: 22:46
 */

namespace local_facial\events;


class event_observers {
    /**
     * @param \core\event\base $event
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function show_detect(\core\event\base $event) {
        global $DB, $USER;

        if (isset($_SESSION['USER']->realuser)) {
            return;
        }

        $eventData = $event->get_data();
        $courseid = $eventData['courseid'];

        // Desativa para Admins e Professores
        $coursecontext = \context_course::instance($courseid);
        if (has_capability('moodle/course:update', $coursecontext)) {
            return;
        }

        $satus = get_config('local_facial', 'enable_course_' . $courseid);

        // Se for para reconehcer no acesso ao curso
        if ($satus == "sim") {

            self::recohecimentoFacial($courseid);

        } // Se tiver que reconhecer só em Modulos
        else if ($satus == "mod") {

            // Se for primeiro acesso ao curso
            // Precisa capturar a foto
            if (strpos($eventData['eventname'], '\course_viewed') > 0) {

                $principalCaptura = $DB->get_field('local_facial_principal', 'captura', [
                    'courseid' => $courseid,
                    'userid' => $USER->id
                ]);
                if (!$principalCaptura) {
                    self::recohecimentoFacial($courseid);
                }
            }

            if (strpos($eventData['eventname'], 'course_module_viewed') > 0) {

                $modId = $eventData['contextinstanceid'];

                $enable = $DB->get_field_select('course_modules', 'course', "id=? AND availability LIKE '%\"type\":\"facial\"%'", [$modId]);

                if ($enable) {
                    self::recohecimentoFacial($courseid);
                }
            }
        }
    }

    private static function recohecimentoFacial($courseid) {
        global $CFG, $PAGE;

        $tempo_course = get_config('local_facial', 'tempo_course_' . $courseid);

        $local_facial = intval(@$_SESSION['LOCAL_FACIAL_' . $courseid]);
        $local_facial_time = $local_facial + (60 * $tempo_course);

        $url = "{$CFG->wwwroot}/local/facial/detect.php?courseid={$courseid}&request_uri={$_SERVER['REQUEST_URI']}";
        $url = str_replace("http:", "https:", $url);

        if ($local_facial == 0 || $local_facial_time < time()) {
            @ob_clean();
            header('Location: ' . $url);
            echo "
                     <script>window.location=\"{$url}\";</script>
                     <meta http-equiv=\"refresh\" content=\"0;url={$url}\"/>";

        } else if (get_config('local_facial', 'redirect_course_' . $courseid)) {
            $tempo = ($tempo_course * 60) - (time() - $local_facial);

            echo "<meta http-equiv=\"refresh\" content=\"{$tempo};url={$url}\"/>";
            $js
                = "setTimeout(function(){
                               window.location=\"{$url}\";
                           }, {$tempo}000 );";

            $PAGE->requires->js_init_code($js);
        }
    }
}