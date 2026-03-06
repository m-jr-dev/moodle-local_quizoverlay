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
 * Library functions for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds nodes to the navigation for users allowed to access the plugin.
 *
 * @package local_quizoverlay
 * @param global_navigation $navigation
 * @return void
 */
function local_quizoverlay_extend_navigation(global_navigation $navigation): void {
    $context = context_system::instance();
    if (!isloggedin() || isguestuser()) {
        return;
    }

    if (!has_capability('local/quizoverlay:manage', $context)) {
        return;
    }

    $url = new moodle_url('/local/quizoverlay/manage.php');
    $node = $navigation->add(
        get_string('pluginname', 'local_quizoverlay'),
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'local_quizoverlay'
    );
    $node->showinflatnavigation = true;

    $node->add(get_string('importcsv', 'local_quizoverlay'), new moodle_url('/local/quizoverlay/index.php'));
    $node->add(get_string('manage', 'local_quizoverlay'), new moodle_url('/local/quizoverlay/manage.php'));
}
