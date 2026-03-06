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
 * Plugin settings.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $category = new admin_category('local_quizoverlay_category', get_string('pluginname', 'local_quizoverlay'));
    $ADMIN->add('localplugins', $category);

    $ADMIN->add('local_quizoverlay_category', new admin_externalpage(
        'local_quizoverlay_import',
        get_string('importcsv', 'local_quizoverlay'),
        new moodle_url('/local/quizoverlay/index.php'),
        'local/quizoverlay:manage'
    ));

    $ADMIN->add('local_quizoverlay_category', new admin_externalpage(
        'local_quizoverlay_manage',
        get_string('manage', 'local_quizoverlay'),
        new moodle_url('/local/quizoverlay/manage.php'),
        'local/quizoverlay:manage'
    ));

    $settings = new admin_settingpage(
        'local_quizoverlay',
        get_string('pluginname', 'local_quizoverlay')
    );

    $settings->add(new admin_setting_heading(
        'local_quizoverlay/generalheading',
        get_string('generalsettings', 'local_quizoverlay'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_quizoverlay/enable_index',
        get_string('enable_index', 'local_quizoverlay'),
        get_string('enable_index_desc', 'local_quizoverlay'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_quizoverlay/enable_manage',
        get_string('enable_manage', 'local_quizoverlay'),
        get_string('enable_manage_desc', 'local_quizoverlay'),
        1
    ));

    $settings->add(new admin_setting_heading(
        'local_quizoverlay/passwordheading',
        get_string('passwordsettings', 'local_quizoverlay'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/passwordpattern',
        get_string('passwordpattern', 'local_quizoverlay'),
        get_string('passwordpattern_desc', 'local_quizoverlay'),
        'EAD-{dd}{mm}-{LL}{NN}',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_heading(
        'local_quizoverlay/csvheading',
        get_string('csvsettings', 'local_quizoverlay'),
        get_string('csvsettings_desc', 'local_quizoverlay')
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_username',
        get_string('csvcol_username', 'local_quizoverlay'),
        get_string('csvcol_username_desc', 'local_quizoverlay'),
        'username',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_shortname',
        get_string('csvcol_shortname', 'local_quizoverlay'),
        get_string('csvcol_shortname_desc', 'local_quizoverlay'),
        'shortname',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_data_in',
        get_string('csvcol_data_in', 'local_quizoverlay'),
        get_string('csvcol_data_in_desc', 'local_quizoverlay'),
        'data_in',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_data_fim',
        get_string('csvcol_data_fim', 'local_quizoverlay'),
        get_string('csvcol_data_fim_desc', 'local_quizoverlay'),
        'data_fim',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_hora_in',
        get_string('csvcol_hora_in', 'local_quizoverlay'),
        get_string('csvcol_hora_in_desc', 'local_quizoverlay'),
        'hora_in',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_hora_fim',
        get_string('csvcol_hora_fim', 'local_quizoverlay'),
        get_string('csvcol_hora_fim_desc', 'local_quizoverlay'),
        'hora_fim',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_quiz',
        get_string('csvcol_quiz', 'local_quizoverlay'),
        get_string('csvcol_quiz_desc', 'local_quizoverlay'),
        'quiz',
        PARAM_RAW_TRIMMED
    ));



    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/timelimitcol_quiz',
        get_string('timelimitcol_quiz', 'local_quizoverlay'),
        get_string('timelimitcol_quiz_desc', 'local_quizoverlay'),
        'timelimit',
        PARAM_RAW_TRIMMED
    ));
    $settings->add(new admin_setting_configtext(
        'local_quizoverlay/csvcol_attempts',
        get_string('csvcol_attempts', 'local_quizoverlay'),
        get_string('csvcol_attempts_desc', 'local_quizoverlay'),
        'attempts',
        PARAM_RAW_TRIMMED
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_quizoverlay/csvcol_extras_required',
        get_string('csvcol_extras_required', 'local_quizoverlay'),
        get_string('csvcol_extras_required_desc', 'local_quizoverlay'),
        '',
        PARAM_RAW_TRIMMED,
        40,
        3
    ));

    $ADMIN->add('local_quizoverlay_category', $settings);
}
