<?php

defined('MOODLE_INTERNAL') || die;
ob_start();

if ($hassiteconfig) {

    global $CFG, $PAGE;

    if (!$PAGE->requires->is_head_done()) {
        $PAGE->requires->jquery();
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/facial/assets/settings.js'));
    }

    $settings = new admin_settingpage('local_facial', get_string('pluginname', 'local_facial'));

    $ADMIN->add('localplugins', $settings);


    $setting = new admin_setting_configtext(
        'local_facial/url',
        "URL",
        "URL do dashboard do Reconhecimento Facial",
        'https://[SUACONTA].faceauthentic.com.br/');
    $settings->add($setting);

    $setting = new admin_setting_configtext(
        'local_facial/token',
        'TOKEN da API',
        'TOKEN da API para o Reconhecimento Facial. Dúvidas entre em contato com a <a href="https://videofront.com.br/contato">VideoFront</a>',
        'HMAC-SHA512-......');
    $settings->add($setting);


    $settings->add(new admin_setting_heading('local_facial/header_2',
        "Mensagem inicial",
        "A mensagem abaixo será mostrado a todos na Primeira captura. <br>
         Explique a importancia da estar próximo da webcam, em local bem iluminado e sem nenhum item 
            que atrapalhe a captura, como mascaras, excesso de maquiagem, etc."));

    $name = 'local_facial/description_primeira';
    $title = 'Descrição para primeira captura';
    $description
        = 'Esta mensagem é apresentada na primeira captura do aluno. 
                    A imagem capturada neste processo será usado em todas as outras comparações!';
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);


    $name = 'local_facial/cssextra';
    $title = 'CSS extra';
    $description = 'Adiciona o CSS na página de captura';
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);


    $courses = $DB->get_records('course');

    foreach ($courses as $course) {

        if ($course->id == 1) {
            continue;
        }

        $settings->add(new admin_setting_heading('local_facial/course' . $course->id, $course->fullname, ''));

        $values = array(
            'nao' => 'Não usar neste curso',
            'sim' => 'Habilitar no acesso ao Curso. Ele só consegue estudar se passar pelo reconhecimento Facial.',
            'mod' => 'Somente nos módulos com o Availability Facial'
        );

        $name = 'local_facial/enable_course_' . $course->id;
        $title = 'Habilitar captura';
        $description = 'Selecione qual modo de captura para o curso <strong>' . $course->fullname . '</strong>';
        $default = 'nao';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $values);
        $settings->add($setting);

        $name = 'local_facial/redirect_course_' . $course->id;
        $title = 'Forçar redirecionamento';
        $description = 'Marque se deseja formçar o redirecionamento quando a página ficar aberta após findar o tempo';
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $settings->add($setting);

        $name = 'local_facial/tempo_course_' . $course->id;
        $title = 'Tempo entre as captura';
        $description = 'Tempo entre uma captura e outra (em minutos)';
        $default = 30;
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $settings->add($setting);

        $name = 'local_facial/description_course_' . $course->id;
        $title = 'Descrição para captura';
        $description = 'Ao capturar o curso <strong>' . $course->fullname . '</strong> mostrar a seguinte mensagem.';
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $settings->add($setting);
    }
}
