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
 * CSV importer and quiz override handler.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizoverlay\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/csvlib.class.php');

use coding_exception;
use csv_import_reader;
use moodle_exception;
use stdClass;

/**
 * CSV importer for creating/updating quiz overrides.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class importer {

    /** @var string[] Base settings keys for required columns. */
    private const BASE_COLUMN_SETTING_KEYS = [
        'csvcol_username',
        'csvcol_shortname',
        'csvcol_data_in',
        'csvcol_data_fim',
        'csvcol_hora_in',
        'csvcol_hora_fim',
        'csvcol_quiz',
        'timelimitcol_quiz',
        'csvcol_attempts',
    ];

    /**
     * Imports a CSV file and creates/updates quiz overrides.
     *
     * @param string $filepath
     * @return array{imported:int, skipped:int, errors:array, successes:array, failures:array}
     * @throws moodle_exception
     */
    public static function import_csv(string $filepath): array {
        global $DB;

        $iid = csv_import_reader::get_new_iid('quizoverlay');
        $cir = new csv_import_reader($iid, 'quizoverlay');

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new moodle_exception('errorreadingfile');
        }

        $csvdelimiter = self::detect_delimiter($content);
        $cir->load_csv_content($content, $csvdelimiter, 'UTF-8');

        $columns = $cir->get_columns();
        if (empty($columns)) {
            throw new moodle_exception('errorreadingfile');
        }

        $columns = array_map(static function($c) {
            $c = \core_text::strtolower(trim((string)$c));
            $c = str_replace([' ', '\t'], '', $c);
            return $c;
        }, $columns);

        $requiredcolumns = self::get_required_columns();
        $missing = array_values(array_diff($requiredcolumns, $columns));
        if (!empty($missing)) {
            throw new moodle_exception('error_missingcolumns', 'local_quizoverlay', '', implode(', ', $missing));
        }

        $colmap = [];
        foreach ($columns as $idx => $name) {
            $colmap[$name] = $idx;
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $successes = [];
        $failures = [];
        $now = time();
        $processed = 0;

        $cir->init();
        $lineno = 1;

        while ($row = $cir->next()) {
            $lineno++;

            if (self::is_empty_row($row)) {
                continue;
            }

            $processed++;

            try {
                $data = self::row_to_data($row, $colmap);

                if ($data->username === '') {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (username vazio).';
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                if ($data->shortname === '') {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (shortname vazio).';
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                if ($data->quiz === '') {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (quiz vazio).';
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $attempts = (int)$data->attempts;
                if ($attempts < 0) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (attempts inválido). attempts=' . $data->attempts;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $timelimitraw = trim((string)$data->timelimit);
                if ($timelimitraw === '') {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (timelimit vazio).';
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                if (!preg_match('/^\d+(?:[\.,]\d+)?$/', $timelimitraw)) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (timelimit inválido). timelimit=' . $data->timelimit;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $timelimitraw = str_replace(',', '.', $timelimitraw);
                $timelimit = (int)round(((float)$timelimitraw) * 3600);
                if ($timelimit < 0) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (timelimit inválido). timelimit=' . $data->timelimit;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $user = $DB->get_record('user', ['username' => $data->username, 'deleted' => 0], 'id,username', IGNORE_MISSING);
                if (!$user) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (usuário não encontrado). username=' . $data->username;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $course = $DB->get_record('course', ['shortname' => $data->shortname], 'id,shortname', IGNORE_MISSING);
                if (!$course) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (curso não encontrado pelo shortname). shortname=' . $data->shortname;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $timeopen = self::parse_datetime($data->data_in, $data->hora_in);
                if ($timeopen === null) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (data_in/hora_in inválidos). data_in='
                        . $data->data_in . ' hora_in=' . $data->hora_in;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $timeclose = self::parse_datetime($data->data_fim, $data->hora_fim);
                if ($timeclose === null) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (data_fim/hora_fim inválidos). data_fim='
                        . $data->data_fim . ' hora_fim=' . $data->hora_fim;
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                if ($timeclose <= $timeopen) {
                    $skipped++;
                    $msg = 'Linha ' . $lineno . ': ignorado (data_fim/hora_fim deve ser maior que data_in/hora_in).';
                    $failures[] = $msg;
                    $errors[] = $msg;
                    continue;
                }

                $password = self::get_or_create_user_password((int)$user->id, $now, $data->data_in);

                $existingrec = $DB->get_record('local_quizoverlay', [
                    'userid' => (int)$user->id,
                    'courseid' => (int)$course->id,
                    'timeopen' => (int)$timeopen,
                    'timeclose' => (int)$timeclose,
                    'quiz' => $data->quiz,
                ], 'id,timecreated', IGNORE_MISSING);

                if (!$existingrec) {
                    $existingrecs = $DB->get_records('local_quizoverlay', [
                        'userid' => (int)$user->id,
                        'courseid' => (int)$course->id,
                        'quiz' => $data->quiz,
                    ], 'timemodified DESC, id DESC', 'id,timecreated', 0, 1);
                    if ($existingrecs) {
                        $existingrec = reset($existingrecs);
                    }
                }

                $record = (object)[
                    'userid' => (int)$user->id,
                    'courseid' => (int)$course->id,
                    'timeopen' => (int)$timeopen,
                    'timeclose' => (int)$timeclose,
                    'quiz' => $data->quiz,
                    'attempts' => (int)$attempts,
                    'timecreated' => $now,
                    'timemodified' => $now,
                ];

                if ($existingrec) {
                    $record->id = (int)$existingrec->id;
                    $record->timecreated = (int)$existingrec->timecreated;
                    $DB->update_record('local_quizoverlay', $record);

                    $msg = 'Linha ' . $lineno . ': atualizado (registro já existia e foi sobreposto). username=' . $data->username;
                    $msg .= ', shortname=' . $data->shortname;
                    $msg .= ', quiz=' . $data->quiz;
                    $msg .= ', data_in=' . $data->data_in;
                    $msg .= ', hora_in=' . $data->hora_in;
                    $msg .= ', data_fim=' . $data->data_fim;
                    $msg .= ', hora_fim=' . $data->hora_fim;
                    $msg .= ', attempts=' . $attempts;
                    $msg .= ', senha_aluno=' . $password . '.';
                    $successes[] = $msg;
                } else {
                    $DB->insert_record('local_quizoverlay', $record);
                    $imported++;

                    $msg = 'Linha ' . $lineno . ': importado. username=' . $data->username;
                    $msg .= ', shortname=' . $data->shortname;
                    $msg .= ', quiz=' . $data->quiz;
                    $msg .= ', data_in=' . $data->data_in;
                    $msg .= ', hora_in=' . $data->hora_in;
                    $msg .= ', data_fim=' . $data->data_fim;
                    $msg .= ', hora_fim=' . $data->hora_fim;
                    $msg .= ', attempts=' . $attempts;
                    $msg .= ', senha_aluno=' . $password . '.';
                    $successes[] = $msg;
                }

                self::create_or_update_quiz_override(
                    (int)$course->id,
                    (int)$user->id,
                    $data->quiz,
                    $timeopen,
                    $timeclose,
                    $timelimit,
                    $attempts,
                    $password
                );

            } catch (coding_exception $e) {
                $skipped++;
                $msg = 'Linha ' . $lineno . ': ignorado (erro interno). ' . $e->getMessage();
                $failures[] = $msg;
                $errors[] = $msg;
            } catch (\Throwable $e) {
                $skipped++;
                $msg = 'Linha ' . $lineno . ': ignorado (' . $e->getMessage() . ')';
                $failures[] = $msg;
                $errors[] = $msg;
            }
        }

        if ($processed === 0) {
            $errors[] = 'Nenhuma linha de dados foi lida do CSV (somente cabeçalho ou arquivo inválido).';
        }

        $cir->cleanup(true);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
            'successes' => $successes,
            'failures' => $failures,
        ];
    }

    /**
     * Returns the required columns (normalized).
     *
     * @return string[]
     */
    public static function get_required_columns(): array {
        $required = [];
        $seen = [];

        foreach (self::BASE_COLUMN_SETTING_KEYS as $key) {
            $val = self::normalize_column_name((string)get_config('local_quizoverlay', $key));
            if ($val === '') {
                continue;
            }

            if (!isset($seen[$val])) {
                $required[] = $val;
                $seen[$val] = true;
            }
        }

        $extra = (string)get_config('local_quizoverlay', 'csvcol_extras_required');
        if ($extra !== '') {
            $parts = preg_split('/\s*,\s*/', $extra, -1, PREG_SPLIT_NO_EMPTY);
            foreach ((array)$parts as $p) {
                $p = self::normalize_column_name((string)$p);
                if ($p === '') {
                    continue;
                }

                if (!isset($seen[$p])) {
                    $required[] = $p;
                    $seen[$p] = true;
                }
            }
        }

        return array_values($required);
    }

    /**
     * Reads a CSV row into a data object using the current column name settings.
     *
     * @param array $row
     * @param array $colmap
     * @return stdClass
     */
    private static function row_to_data(array $row, array $colmap): stdClass {
        $getbysetting = static function(string $settingkey) use ($row, $colmap): string {
            $colname = self::normalize_column_name((string)get_config('local_quizoverlay', $settingkey));
            if ($colname === '' || !array_key_exists($colname, $colmap)) {
                return '';
            }
            $idx = (int)$colmap[$colname];
            return trim((string)($row[$idx] ?? ''));
        };

        $d = new stdClass();
        $d->username = $getbysetting('csvcol_username');
        $d->shortname = $getbysetting('csvcol_shortname');
        $d->data_in = $getbysetting('csvcol_data_in');
        $d->data_fim = $getbysetting('csvcol_data_fim');
        $d->hora_in = $getbysetting('csvcol_hora_in');
        $d->hora_fim = $getbysetting('csvcol_hora_fim');
        $d->quiz = $getbysetting('csvcol_quiz');
        $d->timelimit = $getbysetting('timelimitcol_quiz');
        $d->attempts = $getbysetting('csvcol_attempts');
        return $d;
    }

    /**
     * Creates or updates a quiz override for a single user.
     *
     * @param int $courseid
     * @param int $userid
     * @param string $quizname
     * @param int $timeopen
     * @param int $timeclose
     * @param int $attempts
     * @param string $password
     * @return void
     */
    private static function create_or_update_quiz_override(
        int $courseid,
        int $userid,
        string $quizname,
        int $timeopen,
        int $timeclose,
        int $timelimit,
        int $attempts,
        string $password
    ): void {
        global $DB;

        $quiz = $DB->get_record('quiz', ['course' => $courseid, 'name' => $quizname], 'id,course,name', IGNORE_MISSING);
        if (!$quiz) {
            $msg = 'Quiz não encontrado no curso (o nome do quiz deve ser igual ao valor da coluna quiz). courseid='
                . $courseid . ' quiz=' . $quizname;
            throw new \Exception($msg);
        }

        $override = (object)[
            'quiz' => (int)$quiz->id,
            'groupid' => null,
            'userid' => (int)$userid,
            'timeopen' => (int)$timeopen,
            'timeclose' => (int)$timeclose,
            'timelimit' => (int)$timelimit,
            'attempts' => (int)$attempts,
            'password' => $password,
        ];

        $existing = $DB->get_record('quiz_overrides', [
            'quiz' => (int)$quiz->id,
            'userid' => (int)$userid,
        ], 'id,password', IGNORE_MISSING);

        if ($existing) {
            $override->id = (int)$existing->id;
            if (!empty($existing->password)) {
                $override->password = (string)$existing->password;
            }
            $DB->update_record('quiz_overrides', $override);
        } else {
            $DB->insert_record('quiz_overrides', $override);
        }
    }

    /**
     * Checks whether a CSV row is empty.
     *
     * @param array $row
     * @return bool
     */
    private static function is_empty_row(array $row): bool {
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Detects the delimiter used in a CSV file content.
     *
     * @param string $content
     * @return string
     */
    private static function detect_delimiter(string $content): string {
        $sample = substr($content, 0, 4096);
        $candidates = [',', ';', '\t'];
        $best = ',';
        $bestcount = -1;

        foreach ($candidates as $d) {
            $count = substr_count($sample, $d);
            if ($count > $bestcount) {
                $bestcount = $count;
                $best = $d;
            }
        }

        return $best;
    }

    /**
     * Parses date + time to a timestamp.
     *
     * Supported date formats: dd/mm/yyyy or yyyy-mm-dd
     * Supported time formats: hh:mm or hh:mm:ss
     *
     * @param string $date
     * @param string $time
     * @return int|null
     */
    private static function parse_datetime(string $date, string $time): ?int {
        $date = trim($date);
        $time = trim($time);

        if ($date === '' || $time === '') {
            return null;
        }

        $dparts = null;
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            $dparts = [(int)$m[1], (int)$m[2], (int)$m[3]];
        } else if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            $dparts = [(int)$m[3], (int)$m[2], (int)$m[1]];
        } else {
            return null;
        }

        $starttime = null;
        if (preg_match('/^(\d{2}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
            $starttime = [(int)$m[1], (int)$m[2]];
        }

        if ($dparts === null || $starttime === null) {
            return null;
        }

        [$day, $month, $year] = $dparts;
        [$hour, $minute] = $starttime;

        if (!checkdate($month, $day, $year)) {
            return null;
        }
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return make_timestamp($year, $month, $day, $hour, $minute, 0);
    }

    /**
     * Gets or creates the user's stored password for the overlay.
     *
     * @param int $userid
     * @param int $now
     * @param string $date
     * @return string
     */
    private static function get_or_create_user_password(int $userid, int $now, string $date): string {
        global $DB;

        $existing = $DB->get_record('local_quizoverlay_upass', ['userid' => $userid], 'id,password', IGNORE_MISSING);
        if ($existing) {
            return (string)$existing->password;
        }

        $password = self::format_password($date);
        $rec = (object)[
            'userid' => $userid,
            'password' => $password,
            'timecreated' => $now,
        ];
        $DB->insert_record('local_quizoverlay_upass', $rec);
        return $password;
    }

    /**
     * Formats a password based on the configured pattern and date.
     *
     * @param string $date
     * @return string
     */
    private static function format_password(string $date): string {
        $pattern = trim((string)get_config('local_quizoverlay', 'passwordpattern'));
        if ($pattern === '') {
            $pattern = 'EAD-{dd}{mm}-{LL}{NN}';
        }

        [$day, $month, $year] = self::extract_date_parts($date);

        $replacements = [
            '{dd}' => sprintf('%02d', $day),
            '{mm}' => sprintf('%02d', $month),
            '{yyyy}' => sprintf('%04d', $year),
            '{yy}' => substr(sprintf('%04d', $year), -2),
        ];

        $out = strtr($pattern, $replacements);

        $out = preg_replace_callback('/\{LL\}/', static function(): string {
            return self::random_letters(2);
        }, $out);

        $out = preg_replace_callback('/\{NN\}/', static function(): string {
            return self::random_digits(2);
        }, $out);

        $out = preg_replace_callback('/\{L\}/', static function(): string {
            return self::random_letters(1);
        }, $out);

        $out = preg_replace_callback('/\{N\}/', static function(): string {
            return self::random_digits(1);
        }, $out);

        return (string)$out;
    }

    /**
     * Extracts day/month/year from a supported date string.
     *
     * Supported date formats: dd/mm/yyyy or yyyy-mm-dd
     *
     * @param string $date
     * @return array{0:int,1:int,2:int}
     */
    private static function extract_date_parts(string $date): array {
        $date = trim($date);

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return [(int)$m[1], (int)$m[2], (int)$m[3]];
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            return [(int)$m[3], (int)$m[2], (int)$m[1]];
        }

        $t = time();
        return [(int)date('d', $t), (int)date('m', $t), (int)date('Y', $t)];
    }

    /**
     * Extracts ddmm from a supported date string.
     *
     * @param string $date
     * @return string
     */
    private static function extract_ddmm(string $date): string {
        $date = trim($date);

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return $m[1] . $m[2];
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            return $m[3] . $m[2];
        }

        return '0000';
    }

    /**
     * Generates a random uppercase letters string.
     *
     * @param int $len
     * @return string
     */
    private static function random_letters(int $len): string {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $out;
    }

    /**
     * Generates a random digits string.
     *
     * @param int $len
     * @return string
     */
    private static function random_digits(int $len): string {
        $digits = '0123456789';
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            $out .= $digits[random_int(0, strlen($digits) - 1)];
        }
        return $out;
    }

    /**
     * Normalizes a column name for comparisons.
     *
     * @param string $name
     * @return string
     */
    private static function normalize_column_name(string $name): string {
        $name = \core_text::strtolower(trim($name));
        $name = str_replace([' ', '\t'], '', $name);
        return $name;
    }
}
