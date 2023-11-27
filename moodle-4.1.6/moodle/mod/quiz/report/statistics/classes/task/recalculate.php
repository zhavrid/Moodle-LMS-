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

namespace quiz_statistics\task;

use core\dml\sql_join;
use quiz_attempt;
use quiz_statistics_report;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');

/**
 * Re-calculate question statistics.
 *
 * @package    quiz_statistics
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recalculate extends \core\task\adhoc_task {
    /**
     * Create a new instance of the task.
     *
     * This sets the properties so that only one task will be queued at a time for a given quiz.
     *
     * @param int $quizid
     * @return recalculate
     */
    public static function instance(int $quizid): recalculate {
        $task = new self();
        $task->set_component('quiz_statistics');
        $task->set_userid(get_admin()->id);
        $task->set_custom_data((object)[
            'quizid' => $quizid,
        ]);
        return $task;
    }


    public function get_name(): string {
        return get_string('recalculatetask', 'quiz_statistics');
    }

    public function execute(): void {
        global $DB;
        $dateformat = get_string('strftimedatetimeshortaccurate', 'core_langconfig');
        $data = $this->get_custom_data();
        $quiz = $DB->get_record('quiz', ['id' => $data->quizid]);
        if (!$quiz) {
            mtrace('Could not find quiz with ID ' . $data->quizid . '.');
            return;
        }
        $course = $DB->get_record('course', ['id' => $quiz->course]);
        if (!$course) {
            mtrace('Could not find course with ID ' . $quiz->course . '.');
            return;
        }
        $attemptcount = $DB->count_records('quiz_attempts', ['quiz' => $data->quizid, 'state' => quiz_attempt::FINISHED]);
        if ($attemptcount === 0) {
            mtrace('Could not find any finished attempts for course with ID ' . $data->quizid . '.');
            return;
        }

        mtrace("Re-calculating statistics for quiz {$quiz->name} ({$quiz->id}) " .
            "from course {$course->shortname} ({$course->id}) with {$attemptcount} attempts, start time " .
            userdate(time(), $dateformat) . " ...");

        $qubaids = quiz_statistics_qubaids_condition(
            $quiz->id,
            new sql_join(),
            $quiz->grademethod
        );

        $report = new quiz_statistics_report();
        $report->clear_cached_data($qubaids);
        $report->calculate_questions_stats_for_question_bank($quiz->id);
        mtrace('    Calculations completed at ' . userdate(time(), $dateformat) . '.');
    }
}