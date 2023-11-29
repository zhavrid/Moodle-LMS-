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

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/form/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Feedback Form');
$PAGE->set_heading('Feedback Form');

$form = new local_form_form();

if ($form->is_cancelled()) {
    redirect(new moodle_url('/'));
} else if ($data = $form->get_data()) {
    // Данные формы были отправлены, обрабатываем их
    $record = new stdClass();
    $record->firstname = $data->firstname;
    $record->lastname = $data->lastname;
    $record->username = $data->username;
    $record->email = $data->email;
    $record->timemodified = time();

    // Сохраняем данные в базу данных
    $DB->insert_record('local_form', $record);

    // Вывод данных из формы
    echo $OUTPUT->header();
    echo "<p><strong>First Name:</strong> {$data->firstname}</p>";
    echo "<p><strong>Last Name:</strong> {$data->lastname}</p>";
    echo "<p><strong>Username:</strong> {$data->username}</p>";
    echo "<p><strong>Email:</strong> {$data->email}</p>";



    echo $OUTPUT->footer();
} else {
    // Вывод формы для заполнения
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
