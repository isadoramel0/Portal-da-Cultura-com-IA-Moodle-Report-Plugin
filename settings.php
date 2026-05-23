<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('report_iareport', get_string('pluginname', 'report_iareport'));

    $settings->add(new admin_setting_configtext(
        'report_iareport/apikey',
        'Chave da API Gemini',
        'Insira sua chave da API do Google Gemini. Obtida em https://aistudio.google.com/app/apikey',
        '',
        PARAM_TEXT
    ));

    $ADMIN->add('reports', $settings);
}