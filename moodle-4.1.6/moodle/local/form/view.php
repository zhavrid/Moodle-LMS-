<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_form
 * @category    string
 * @copyright   LMS<zhavridanna7@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once('../../config.php');
require_once('form.php');

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/form/view.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('View Feedback Form Data');
$PAGE->set_heading('View Feedback Form Data');

echo $OUTPUT->header();


$sql = "SELECT * FROM {local_form}"; 
$data = $DB->get_records_sql($sql);

if (!empty($data)) {
    echo '<h2>Feedback Form Data</h2>';
    echo '<table border="1">';
    echo '<tr><th>First Name</th><th>Last Name</th><th>Username</th><th>Email</th></tr>';

    foreach ($data as $record) {
        echo "<tr><td>{$record->firstname}</td><td>{$record->lastname}</td><td>{$record->username}</td><td>{$record->email}</td></tr>";
    }

    echo '</table>';
} else {
    echo '<p>No feedback form data available.</p>';
}

echo $OUTPUT->footer();
