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
    $record = new stdClass();
    $record->firstname = $data->firstname;
    $record->lastname = $data->lastname;
    $record->username = $data->username;
    $record->email = $data->email;
    $record->timemodified = time();

    $existing_user = $DB->get_record('local_form', array('username' => $data->username));

    if ($existing_user) {
        // обновляю last_login перед увеличением login_count
        $existing_user->timemodified = $record->timemodified;
        $existing_user->last_login = $existing_user->timemodified; 
        $existing_user->login_count += 1;
        $DB->update_record('local_form', $existing_user);
    } else {
        $record->login_count = 1;
        $record->last_login = $record->timemodified;
        $DB->insert_record('local_form', $record);
    }

    $DB->insert_record('local_form', $record);

    $arr = array(
        'properties' => array(
            array(
                'property' => 'email',
                'value' => $data->email
            ),
            array(
                'property' => 'firstname',
                'value' => $data->firstname
            ),
            array(
                'property' => 'lastname',
                'value' => $data->lastname
            )
        )
    );
    
    $post_json = json_encode($arr);
    
    $hapikey = 'pat-eu1-f0287396-1d2f-4e05-bb1f-eb78094cff6a';
    $endpoint = 'https://api.hubapi.com/contacts/v1/contact/';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $hapikey
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);
    
    curl_close($ch);
    
    echo "curl Errors: " . $curl_errors;
    echo "\nStatus code: " . $status_code;
    echo "\nResponse: " . $response;    

    echo $OUTPUT->header();
    echo "<p><strong>First Name:</strong> {$data->firstname}</p>";
    echo "<p><strong>Last Name:</strong> {$data->lastname}</p>";
    echo "<p><strong>Username:</strong> {$data->username}</p>";
    echo "<p><strong>Email:</strong> {$data->email}</p>";

    echo $OUTPUT->footer();
} else {
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
