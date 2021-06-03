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
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once ($CFG->dirroot.'/theme/itragsa/locallib.php');

global $USER;
global $DB;
global $COURSE;
$context = context_course::instance(SITEID);
$contextg = context_course::instance($COURSE->id);


// Idioma de la página
$pos = strpos($PAGE->bodyclasses, 'lang-');
$lang = substr($PAGE->bodyclasses, $pos+5, 2);

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'false');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}

$themename = $PAGE->theme->name;
$config = get_config('theme_' . $themename);

$sectiontitle = $PAGE->title;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$courseid = optional_param('course', 0, PARAM_INT);
$userid = optional_param('id', $USER->id, PARAM_INT);

$context = context_course::instance(SITEID); 

if(is_guest($contextg, $USER)){
    $isGuest = true;
}
else{
    $isGuest=false;
}

$templatecontext = [
    'userlongname' => empty($user) ? '' : $user->firstname . ' ' . $user->lastname,
    'image_url' => empty($user) ? '' : moodle_url::make_pluginfile_url(context_user::instance($user->id, IGNORE_MISSING)->id, 'user', 'icon', 0, '/', 'f1?t='.time()),
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    // 'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'themename' => $themename,
    'sectiontitle' => $sectiontitle,
    'coursecreator' => $coursecreator ?? '',
    'userid' => $USER->id,
    'isGuest' => $isGuest,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_itragsa/standard', $templatecontext);

