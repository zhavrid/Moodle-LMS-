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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

class local_form_form extends moodleform {
  
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'firstname', get_string('firstname', 'local_form'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'lastname', get_string('lastname', 'local_form'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'username', get_string('username', 'local_form'));
        $mform->setType('username', PARAM_TEXT);
        $mform->addRule('username', get_string('required'), 'required', null, 'client');
        $mform->addRule('username', get_string('toolong', 'local_form'), 'maxlength', 7, 'client');
        $mform->addRule('username', 'Username should not contain uppercase letters', 'regex', '/^[a-z]*$/', 'client');
        
        $mform->addElement('text', 'email', get_string('email'), array('size' => '64'));
        $mform->setType('email', PARAM_RAW);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');
        $mform->addRule('email', get_string('invalidemail'), 'validate_email', null, 'client');
        $mform->addRule('email', 'Email should contain @ symbol', 'regex', '/@/', 'client');
        $mform->addRule('email', 'Email should not contain numbers between @ and .com', 'regex', '/^[a-z0-9]+@[a-z]+\.[a-z]{2,}$/', 'client');
        $mform->addRule('email', get_string('maximumchars', '', 254), 'maxlength', 254, 'client');
        
        $this->add_action_buttons(true, get_string('submit', 'local_form'));
    }
}
