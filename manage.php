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
 * Management page for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

use local_quizoverlay\output\appointment_list;

require_login();

$context = context_system::instance();
require_capability('local/quizoverlay:manage', $context);

$username = optional_param('username', '', PARAM_RAW_TRIMMED);
$shortname = optional_param('shortname', '', PARAM_RAW_TRIMMED);
$quiz = optional_param('quiz', '', PARAM_RAW_TRIMMED);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

if ($page < 0) {
    $page = 0;
}

$allowedperpage = [20, 50, 100, 200];
if (!in_array($perpage, $allowedperpage, true)) {
    $perpage = 20;
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/quizoverlay/manage.php', [
    'username' => $username,
    'shortname' => $shortname,
    'quiz' => $quiz,
    'page' => $page,
    'perpage' => $perpage,
]));
$PAGE->set_title(get_string('manageappointments', 'local_quizoverlay'));
$PAGE->set_heading(get_string('pluginname', 'local_quizoverlay'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css(new moodle_url('/local/quizoverlay/styles.css'));

global $DB;

$params = [];
$conditions = [];

if ($username !== '') {
    $conditions[] = $DB->sql_like('u.username', ':username', false, false);
    $params['username'] = '%' . $DB->sql_like_escape($username) . '%';
}

if ($shortname !== '') {
    $conditions[] = $DB->sql_like('c.shortname', ':shortname', false, false);
    $params['shortname'] = '%' . $DB->sql_like_escape($shortname) . '%';
}

if ($quiz !== '') {
    $conditions[] = $DB->sql_like('a.quiz', ':quiz', false, false);
    $params['quiz'] = '%' . $DB->sql_like_escape($quiz) . '%';
}

$where = '';
if (!empty($conditions)) {
    $where = 'AND ' . implode(' AND ', $conditions);
}

$countsql = "SELECT COUNT(1)
               FROM {local_quizoverlay} a
               JOIN {user} u ON u.id = a.userid
               JOIN {course} c ON c.id = a.courseid
          LEFT JOIN {local_quizoverlay_upass} p ON p.userid = a.userid
              WHERE 1=1 $where";

$total = (int)$DB->count_records_sql($countsql, $params);

$sql = "SELECT a.id, a.userid, a.courseid, a.timeopen, a.timeclose, a.quiz, a.attempts,
               u.username, " . $DB->sql_fullname('u.firstname', 'u.lastname') . " AS fullname,
               c.fullname AS coursename, c.shortname,
               p.password
          FROM {local_quizoverlay} a
          JOIN {user} u ON u.id = a.userid
          JOIN {course} c ON c.id = a.courseid
     LEFT JOIN {local_quizoverlay_upass} p ON p.userid = a.userid
         WHERE 1=1 $where
      ORDER BY a.timeopen ASC";

$rows = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
foreach ($rows as $r) {
    $r->timeopenstr = userdate((int)$r->timeopen);
    $r->timeclosestr = userdate((int)$r->timeclose);

    $r->timelimitstr = '-';
    $quizkey = (int)$r->courseid . '|' . $r->quiz;
    if (!isset($quizcache)) {
        $quizcache = [];
    }
    if (!isset($overridecache)) {
        $overridecache = [];
    }

    if (!array_key_exists($quizkey, $quizcache)) {
        $quizcache[$quizkey] = $DB->get_field('quiz', 'id', [
            'course' => (int)$r->courseid,
            'name' => $r->quiz,
        ], IGNORE_MISSING);
    }

    if (!empty($quizcache[$quizkey])) {
        $overridekey = (int)$r->userid . '|' . (int)$quizcache[$quizkey] . '|' . (int)$r->timeopen . '|' . (int)$r->timeclose;
        if (!array_key_exists($overridekey, $overridecache)) {
            $overridecache[$overridekey] = $DB->get_field('quiz_overrides', 'timelimit', [
                'quiz' => (int)$quizcache[$quizkey],
                'userid' => (int)$r->userid,
                'timeopen' => (int)$r->timeopen,
                'timeclose' => (int)$r->timeclose,
            ], IGNORE_MISSING);
        }

        if (!empty($overridecache[$overridekey])) {
            $r->timelimitstr = format_time((int)$overridecache[$overridekey]);
        }
    }
}

$baseurl = new moodle_url('/local/quizoverlay/manage.php', [
    'username' => $username,
    'shortname' => $shortname,
    'quiz' => $quiz,
    'perpage' => $perpage,
]);

$pagingbar = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);

$renderable = new appointment_list(array_values($rows), true, [
    'username' => $username,
    'shortname' => $shortname,
    'quiz' => $quiz,
    'page' => $page,
    'perpage' => $perpage,
    'total' => $total,
    'pagingbar' => $pagingbar,
    'reseturl' => (new moodle_url('/local/quizoverlay/manage.php')),
]);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageappointments', 'local_quizoverlay'));

echo $OUTPUT->render($renderable);

echo $OUTPUT->footer();
