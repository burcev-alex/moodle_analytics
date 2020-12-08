<?php

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
 * Web service external functions and service definitions.
 *
 * @package    wsanalyticalsystem
 * @copyright  2020 Burcev Alexander
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'wsanalyticalsystem_ping' => array(
                'classname'   => 'wsanalyticalsystem_external',
                'methodname'  => 'ping',
                'classpath'   => 'local/wsanalyticalsystem/externallib.php',
                'description' => 'Return FIRSTNAME.',
                'type'        => 'read',
        ),
        'wsanalyticalsystem_question_list_by_courses' => array(
                'classname'   => 'wsanalyticalsystem_external',
                'methodname'  => 'question_list_by_courses',
                'classpath'   => 'local/wsanalyticalsystem/externallib.php',
                'description' => 'Return question list of courses',
                'type'        => 'read',
		),
		'wsanalyticalsystem_quizzes_by_courses' => array(
			'classname'     => 'wsanalyticalsystem_external',
			'methodname'    => 'quizzes_by_courses',
			'description'   => 'Returns a list of quizzes in a provided list of courses,
								if no list is provided all quizzes that the user can view will be returned.',
			'type'          => 'read',
		),
		'wsanalyticalsystem_user_attempts' => array(
			'classname'     => 'wsanalyticalsystem_external',
			'methodname'    => 'user_attempts',
			'description'   => 'Return a list of attempts for the given quiz and user.',
			'type'          => 'read',
		),
		'wsanalyticalsystem_attempt_review' => array(
			'classname'     => 'wsanalyticalsystem_external',
			'methodname'    => 'attempt_review',
			'description'   => 'Returns review information for the given finished attempt, can be used by users or teachers.',
			'type'          => 'read',
		),
		'wsanalyticalsystem_pages_by_courses' => array(
			'classname'     => 'wsanalyticalsystem_external',
			'methodname'    => 'pages_by_courses',
			'description'   => 'Returns a list of pages in a provided list of courses, if no list is provided all pages that the user can view will be returned.',
			'type'          => 'read',
		),
		'wsanalyticalsystem_enrolled_users' => array(
			'classname' => 'wsanalyticalsystem_external',
			'methodname' => 'enrolled_users',
			'description' => 'Get enrolled users by course id.',
			'type' => 'read',
		),
		'wsanalyticalsystem_send_messages' => array(
			'classname' => 'wsanalyticalsystem_external',
			'methodname' => 'send_messages',
			'description' => 'Send instant messages',
			'type' => 'write',
			'ajax' => true,
		),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Analytical System' => array(
    	'functions' => array (
                    'wsanalyticalsystem_ping', 
					'wsanalyticalsystem_question_list_by_courses',
					'wsanalyticalsystem_quizzes_by_courses',
					'wsanalyticalsystem_user_attempts',
					'wsanalyticalsystem_attempt_review',
					'wsanalyticalsystem_pages_by_courses',
					'wsanalyticalsystem_enrolled_users',
					'wsanalyticalsystem_send_messages'
            ),
            'restrictedusers' => 0,
            'enabled' => 1,
    )
);
