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
 * Privacy provider implementation.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizoverlay\privacy;

use context;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata about this plugin's stored data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_quizoverlay', [
            'userid' => 'privacy:metadata:local_quizoverlay:userid',
            'courseid' => 'privacy:metadata:local_quizoverlay:courseid',
            'timeopen' => 'privacy:metadata:local_quizoverlay:timeopen',
            'timeclose' => 'privacy:metadata:local_quizoverlay:timeclose',
            'quiz' => 'privacy:metadata:local_quizoverlay:quiz',
            'attempts' => 'privacy:metadata:local_quizoverlay:attempts',
            'timecreated' => 'privacy:metadata:local_quizoverlay:timecreated',
            'timemodified' => 'privacy:metadata:local_quizoverlay:timemodified',
        ], 'privacy:metadata:local_quizoverlay');

        $collection->add_database_table('local_quizoverlay_upass', [
            'userid' => 'privacy:metadata:local_quizoverlay_upass:userid',
            'password' => 'privacy:metadata:local_quizoverlay_upass:password',
            'timecreated' => 'privacy:metadata:local_quizoverlay_upass:timecreated',
        ], 'privacy:metadata:local_quizoverlay_upass');

        return $collection;
    }

    /**
     * Gets the list of contexts that contain user information for the given user.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $contextlist->add_from_sql(
            "SELECT ctx.id
               FROM {context} ctx
              WHERE ctx.contextlevel = :contextlevel",
            ['contextlevel' => CONTEXT_SYSTEM]
        );

        return $contextlist;
    }

    /**
     * Exports personal data for the given approved contexts.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $systemcontext = context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        $records = $DB->get_records('local_quizoverlay', ['userid' => $userid]);
        if ($records) {
            writer::with_context($systemcontext)->export_data(['local_quizoverlay'], (object)[
                'overrides' => array_values($records),
            ]);
        }

        $upass = $DB->get_record('local_quizoverlay_upass', ['userid' => $userid], '*', IGNORE_MISSING);
        if ($upass) {
            writer::with_context($systemcontext)->export_data(['local_quizoverlay_upass'], $upass);
        }
    }

    /**
     * Deletes all user data for the given context.
     *
     * @param context $context
     * @return void
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $DB->delete_records('local_quizoverlay');
        $DB->delete_records('local_quizoverlay_upass');
    }

    /**
     * Deletes user data for the given approved contexts.
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $systemcontext = context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        $DB->delete_records('local_quizoverlay', ['userid' => $userid]);
        $DB->delete_records('local_quizoverlay_upass', ['userid' => $userid]);
    }

    /**
     * Gets the list of users who have data within a context.
     *
     * @param userlist $userlist
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $sql = "SELECT userid FROM {local_quizoverlay}";
        $userlist->add_from_sql('userid', $sql, []);

        $sql = "SELECT userid FROM {local_quizoverlay_upass}";
        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Deletes data for multiple users within a single context.
     *
     * @param approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $DB->delete_records_select('local_quizoverlay', "userid $insql", $inparams);
        $DB->delete_records_select('local_quizoverlay_upass', "userid $insql", $inparams);
    }
}
