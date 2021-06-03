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
 * A two column layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

 // Custom LearningWorks functions.

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

$context = context_course::instance($COURSE->id);

global $DB;
global $COURSE;
global $CFG;
// require_once($CFG->libdir.'/coursecatlib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'false');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$acces_login = "inserver";

if(is_guest($context, $USER)){
    $isGuest = true;
}
else{
    $isGuest=false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$context = context_course::instance($COURSE->id);
$mycourses = enrol_get_all_users_courses($USER->id, true, null);
$idCourses = array();
foreach($mycourses as $mycourse){
    array_push($idCourses, $mycourse->id);
}

/*__________ Información para el slider de cursos disponibles__________ */
//Cursos con autoenrol
$courses_autoenrol = $DB->get_records_sql('SELECT courseid, enrol, status
    FROM {enrol}
    WHERE enrol = "self" AND status = 0 ');
$courses_autoenrol_print = false;
$course_autoenrol_data = "";
$array_auto_enroll = array();
if(!empty($courses_autoenrol) || !isset($courses_autoenrol)) {
    $courses_autoenrol_print = true;
    foreach ($courses_autoenrol as $course_enrol) {
        $course_autoenrol_info = $DB->get_record('course', array('id' => $course_enrol->courseid));
        // require_once($CFG->libdir . '/coursecatlib.php');
        $course_img = new core_course_list_element($course_autoenrol_info);

        $outputimage = '';
        foreach ($course_img->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $imagepath = '/' . $file->get_contextid() .
                        '/' . $file->get_component() .
                        '/' . $file->get_filearea() .
                        $file->get_filepath() .
                        $file->get_filename();
                $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath,
                        false);
                $outputimage = $imageurl;
                // Use the first image found.
                break;
            }
        }

        if(!in_array($course_enrol->courseid,$idCourses) || has_capability('moodle/user:viewalldetails', $context)){
            $object = new stdClass();
            $object->name = $course_autoenrol_info->fullname;
            $object->id = $course_autoenrol_info->id;
            $object->courseimage = $outputimage;
            array_push($array_auto_enroll, $object);
        }
    }
}
//Cursos con solicitud de matriculación
$courses_apply = $DB->get_records_sql('SELECT courseid, enrol, status
    FROM {enrol}
    WHERE enrol = "apply" AND status = 0 ');
$courses_apply_print = false;
$course_apply_data = "";
$array_apply = array();
if(!empty($courses_apply) || !isset($courses_apply)) {
    $courses_apply_print = true;
    foreach ($courses_apply as $course_enrol) {
        $course_apply_info = $DB->get_record('course', array('id' => $course_enrol->courseid));
        // require_once($CFG->libdir . '/coursecatlib.php');
        $course_img = new core_course_list_element($course_apply_info);

        $outputimage = '';
        foreach ($course_img->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $imagepath = '/' . $file->get_contextid() .
                        '/' . $file->get_component() .
                        '/' . $file->get_filearea() .
                        $file->get_filepath() .
                        $file->get_filename();
                $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath,
                        false);
                $outputimage = $imageurl;
                // Use the first image found.
                break;
            }
        }
        
        if(!in_array($course_enrol->courseid,$idCourses) || has_capability('moodle/user:viewalldetails', $context)){
            $object = new stdClass();
            $object->name = $course_apply_info->fullname;
            $object->id = $course_apply_info->id;
            $object->courseimage = $outputimage;
            array_push($array_apply, $object);
        }
    }
}

//Cursos con gestión del profesor
$teacher_courses_print = false;
$array_teacher_courses = array();
$courses = enrol_get_users_courses($USER->id, true); // me devuelve los cursos en los que estoy apuntado
if(!empty($courses) || !isset($courses)) {
    $teacher_courses_print = true; // si encuentra resultados muestra el slider
    foreach ($courses as $course) {  // por cada curso
        $context = context_course::instance($course->id, MUST_EXIST); // dentro del contexto de cada curso
        if (has_capability ('moodle/course:viewhiddencourses', $context)) { // esta capability solo la tienen manager, coursecreator, teacher, nonedteacher
            // IMAGEN
            $course_img = new core_course_list_element($course);
            $outputimage = '';
            foreach ($course_img->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $imagepath = '/' . $file->get_contextid() .
                                 '/' . $file->get_component() .
                                 '/' . $file->get_filearea() .
                                       $file->get_filepath() .
                                       $file->get_filename();
                    $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath, false);
                    $outputimage = $imageurl;
                    // Use the first image found.
                    break;
                }
            }
            $object = new stdClass();
            $object->name = $course->fullname;
            $object->id = $course->id;
            $object->courseimage = $outputimage;
            array_push($array_teacher_courses, $object);
        }
    }
}

// Cursos con matriculación stripepayment
$stripepayment_courses_print = false;
$array_stripepayment_courses = array();
$courses = $DB->get_records_sql('SELECT courseid, enrol, status
                                FROM {enrol}
                                WHERE enrol = "stripepayment" AND status = 0 ');
if(!empty($courses) || !isset($courses)) {
    foreach ($courses as $course) {
        $course_stripepayment_info = $DB->get_record('course', array('id' => $course->courseid));
        $course_img = new core_course_list_element($course_stripepayment_info);
        $context = context_course::instance($course->courseid, MUST_EXIST);
        if (!is_enrolled($context, $USER)) {
            $stripepayment_courses_print = true;
            //$course_img = new core_course_list_element($course);
            //$outputimage = '';
            foreach ($course_img->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $imagepath = '/' . $file->get_contextid() .
                                '/' . $file->get_component() .
                                '/' . $file->get_filearea() .
                                    $file->get_filepath() .
                                    $file->get_filename();
                    $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath, false);
                    $outputimage = $imageurl;
                    break;
                }
            }
            $object = new stdClass();
            $object->name = $course_stripepayment_info->fullname;
            $object->id = $course->courseid;
            $object->courseimage = $outputimage;
            array_push($array_stripepayment_courses, $object);
        }
    }
}


$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'userid' => $USER->id,
    'exit_url' => (string) new moodle_url('/access/login_'. $acces_login .'/logout.php', array('sesskey' => sesskey())),
    'firstname' => $USER->firstname,
    'courses_autoenrol_print' => $courses_autoenrol_print,
    'array_autoenroll' => $array_auto_enroll,
    'array_apply' => $array_apply,
    'courses_apply_print' => $courses_apply_print,
    'array_stripepayment_courses' => $array_stripepayment_courses,
    'stripepayment_courses_print' => $stripepayment_courses_print,
    'isGuest' => $isGuest,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_itragsa/mydashboard', $templatecontext);