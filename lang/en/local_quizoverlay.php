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
 * English language strings for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['col_attempts'] = 'Attempts';
$string['col_course'] = 'Course';
$string['col_password'] = 'Password';
$string['col_quiz'] = 'Quiz';
$string['col_timeclose'] = 'End';
$string['col_timelimit'] = 'Time limit';
$string['col_timeopen'] = 'Start';
$string['col_username'] = 'Username';

$string['csvcol_attempts'] = 'Column: attempts';
$string['csvcol_attempts_desc'] = 'CSV header column that contains the number of allowed attempts (use 0 for unlimited).';
$string['csvcol_data_fim'] = 'Column: data_fim';
$string['csvcol_data_fim_desc'] = 'CSV header column that contains the end date (dd/mm/yyyy or yyyy-mm-dd).';
$string['csvcol_data_in'] = 'Column: data_in';
$string['csvcol_data_in_desc'] = 'CSV header column that contains the start date (dd/mm/yyyy or yyyy-mm-dd).';
$string['csvcol_extras_required'] = 'Extra required columns';
$string['csvcol_extras_required_desc'] = 'Optional. Comma-separated list of additional required columns in the CSV header.';
$string['csvcol_hora_fim'] = 'Column: hora_fim';
$string['csvcol_hora_fim_desc'] = 'CSV header column that contains the end time (hh:mm).';
$string['csvcol_hora_in'] = 'Column: hora_in';
$string['csvcol_hora_in_desc'] = 'CSV header column that contains the start time (hh:mm).';
$string['csvcol_quiz'] = 'Column: quiz';
$string['csvcol_quiz_desc'] = 'CSV header column that contains the quiz name (must match the quiz activity name in the course).';
$string['csvcol_shortname'] = 'Column: shortname';
$string['csvcol_shortname_desc'] = 'CSV header column that contains the course shortname.';
$string['csvcol_username'] = 'Column: username';
$string['csvcol_username_desc'] = 'CSV header column that contains the user username.';

$string['csvfile'] = 'CSV file';
$string['csvrequiredcolumns'] = 'Required columns';
$string['csvsettings'] = 'CSV column settings';
$string['csvsettings_desc'] = 'Define the column names expected in the CSV header. Names are matched case-insensitively and ignoring spaces.';

$string['enable_index'] = 'Enable CSV import page (index.php)';
$string['enable_index_desc'] = 'Allow access to the CSV import page.';
$string['enable_manage'] = 'Enable admin page (manage.php)';
$string['enable_manage_desc'] = 'Allow access to the admin listing/management page.';

$string['error_missingcolumns'] = 'Missing required columns: {$a}';
$string['errors'] = 'Errors';

$string['filter_perpage'] = 'Per page';
$string['filter_quiz'] = 'Quiz (name)';
$string['filter_shortname'] = 'Course (shortname)';
$string['filter_username'] = 'Username';

$string['generalsettings'] = 'General settings';

$string['importcsv'] = 'Import CSV';
$string['imported'] = 'Imported';
$string['importresult'] = 'Import result';

$string['manage'] = 'Manage';
$string['manageappointments'] = 'Manage overrides';

$string['passwordpattern'] = 'Student password pattern';
$string['passwordpattern_desc'] = 'Define the password pattern used for new students. Variables: {dd} (day), {mm} (month), {yyyy} (year), {yy} (2-digit year), {LL} (2 letters), {NN} (2 digits), {L} (1 letter), {N} (1 digit).';
$string['passwordsettings'] = 'Password settings';

$string['pluginname'] = 'Quiz override';

$string['privacy:metadata:local_quizoverlay'] = 'Stored quiz override records.';
$string['privacy:metadata:local_quizoverlay:attempts'] = 'Allowed attempts.';
$string['privacy:metadata:local_quizoverlay:courseid'] = 'The ID of the course.';
$string['privacy:metadata:local_quizoverlay:quiz'] = 'Quiz name.';
$string['privacy:metadata:local_quizoverlay:timeclose'] = 'Override end time.';
$string['privacy:metadata:local_quizoverlay:timecreated'] = 'Time created.';
$string['privacy:metadata:local_quizoverlay:timemodified'] = 'Time modified.';
$string['privacy:metadata:local_quizoverlay:timeopen'] = 'Override start time.';
$string['privacy:metadata:local_quizoverlay:userid'] = 'The ID of the user.';

$string['privacy:metadata:local_quizoverlay_upass'] = 'Stored password for the user.';
$string['privacy:metadata:local_quizoverlay_upass:password'] = 'Generated password.';
$string['privacy:metadata:local_quizoverlay_upass:timecreated'] = 'Time created.';
$string['privacy:metadata:local_quizoverlay_upass:userid'] = 'The ID of the user.';

$string['resetfilters'] = 'Reset filters';
$string['search'] = 'Search';
$string['skipped'] = 'Skipped';

$string['timelimitcol_quiz'] = 'Column: timelimit';
$string['timelimitcol_quiz_desc'] = 'CSV header column that contains the time limit (in hours) to apply as timelimit (in seconds) in the quiz override. Example: 1 = 3600.';

$string['totalresults'] = 'Total';
