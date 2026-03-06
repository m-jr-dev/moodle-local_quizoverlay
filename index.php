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
 * CSV import page for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

use local_quizoverlay\form\import_form;
use local_quizoverlay\local\importer;

require_login();

$context = context_system::instance();
require_capability('local/quizoverlay:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/quizoverlay/index.php'));
$PAGE->set_title(get_string('importcsv', 'local_quizoverlay'));
$PAGE->set_heading(get_string('pluginname', 'local_quizoverlay'));
$PAGE->set_pagelayout('admin');

$form = new import_form();

$result = null;
if ($data = $form->get_data()) {
    $draftitemid = $data->csvfile;
    $usercontext = context_user::instance($USER->id);

    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);
    $file = reset($files);

    if ($file) {
        $tmpdir = make_temp_directory('local_quizoverlay');
        $tmppath = $tmpdir . '/import_' . $USER->id . '_' . time() . '.csv';
        $file->copy_content_to($tmppath);
        $result = importer::import_csv($tmppath);
        @unlink($tmppath);
    }
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('importcsv', 'local_quizoverlay'));

$form->display();

$requiredcols = implode(', ', importer::get_required_columns());
echo html_writer::tag('p', get_string('csvrequiredcolumns', 'local_quizoverlay') . ': ' . html_writer::tag('code', $requiredcols));

if ($result !== null) {
    echo $OUTPUT->notification(get_string('importresult', 'local_quizoverlay') . ': ' .
        get_string('imported', 'local_quizoverlay') . ' ' . (int)$result['imported'] . ' | ' .
        get_string('skipped', 'local_quizoverlay') . ' ' . (int)$result['skipped'], 'notifysuccess');

    if (!empty($result['errors'])) {
        echo $OUTPUT->notification(implode('<br>', array_map('s', (array)$result['errors'])), 'notifyproblem');
    }

    if (!empty($result['successes'])) {
        echo $OUTPUT->notification(implode('<br>', array_map('s', (array)$result['successes'])), 'notifysuccess');
    }
}

echo $OUTPUT->footer();
