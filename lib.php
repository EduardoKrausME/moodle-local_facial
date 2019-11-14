<?php

/**
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_facial_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    $systemcontext = context_system::instance();
    if (
        has_capability('moodle/user:create', $systemcontext) ||
        has_capability('moodle/user:update', $systemcontext)
    ) {
        $courseid = 1;
        if ($course)
            $courseid = $course->id;

        $url = new moodle_url('/local/facial/', array('userid' => $user->id, 'courseid' => $courseid));
        $urlText = $url->out();

        $node = new core_user\output\myprofile\node('contact', 'localfacial3',
            'Reconhecimento Facial', null, null,
            "<a href='{$urlText}'>Ver todas as capturas</a>");
        $tree->add_node($node);
    }

    return true;
}