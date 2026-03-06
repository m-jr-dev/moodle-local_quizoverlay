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
 * Renderable list of quiz overrides.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizoverlay\output;

use renderable;
use renderer_base;
use templatable;

/**
 * Renderable list for displaying quiz override appointments.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class appointment_list implements renderable, templatable {
    /** @var array */
    private $rows;

    /** @var bool */
    private $showusername;

    /** @var array */
    private $context;

    /**
     * Creates a renderable appointment list.
     *
     * @param array $rows
     * @param bool $showusername
     */
    public function __construct(array $rows, bool $showusername = false, array $context = []) {
        $this->rows = $rows;
        $this->showusername = $showusername;
        $this->context = $context;
    }

    /**
     * Exports data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $out = [];
        foreach ($this->rows as $r) {
            $out[] = [
                'username' => $r->username ?? '',
                'fullname' => $r->fullname ?? '',
                'course' => $r->coursename ?? '',
                'shortname' => $r->shortname ?? '',
                'timeopen' => $r->timeopenstr ?? '',
                'timeclose' => $r->timeclosestr ?? '',
                'timelimit' => $r->timelimitstr ?? '-',
                'quiz' => $r->quiz ?? '',
                'attempts' => $r->attempts ?? 0,
                'password' => $r->password ?? '',
                'showusername' => $this->showusername,
            ];
        }

        $colspan = $this->showusername ? 8 : 7;

        $filters = $this->context['filters'] ?? [
            'username' => $this->context['username'] ?? '',
            'shortname' => $this->context['shortname'] ?? '',
            'quiz' => $this->context['quiz'] ?? '',
        ];

        $perpage = (int)($this->context['perpage'] ?? 25);
        $perpagevalues = [10, 25, 50, 100, 200];
        $perpageoptions = [];
        foreach ($perpagevalues as $v) {
            $perpageoptions[] = [
                'value' => $v,
                'label' => (string)$v,
                'selected' => ($v === $perpage),
            ];
        }

        return [
            'rows' => $out,
            'showusername' => $this->showusername,
            'filters' => $filters,
            'perpageoptions' => $perpageoptions,
            'total' => $this->context['total'] ?? 0,
            'pagingbar' => $this->context['pagingbar'] ?? '',
            'reseturl' => $this->context['reseturl'] ?? '',
            'colspan' => $colspan,
        ];
    }
}
