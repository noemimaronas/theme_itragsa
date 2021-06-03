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
user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

global $USER;

$course = new core_course_list_element($COURSE);
$context = context_course::instance($COURSE->id);

$outputimage = '';
foreach ($course->get_course_overviewfiles() as $file) {
    if ($file->is_valid_image()) {
        $imagepath = '/' . $file->get_contextid() .
        '/' . $file->get_component() .
        '/' . $file->get_filearea() .
        $file->get_filepath() .
        $file->get_filename();
        $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath, false);
    }
}

if (!isset($imageurl)) {
    $imageurl = $CFG->wwwroot . '/theme/itragsa/pix/course_default_img.jpg';
}

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
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'navdraweropen'         => $navdraweropen,
    'courseimage'               => $imageurl,
    'courseid'                  => $COURSE->id,
    'coursename'                => $COURSE->fullname,
    'userid' => $USER->id,
    'exit_url' => (string) new moodle_url('/access/login_'. $acces_login .'/logout.php', array('sesskey' => sesskey())),
    'isGuest' => $isGuest,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_itragsa/course', $templatecontext);

