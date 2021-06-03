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

$themename = $PAGE->theme->name;
$config = get_config('theme_' . $themename);

$sectiontitle = $PAGE->title;

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


// PERFIL CURSO
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$courseid = optional_param('course', 0, PARAM_INT);

// Determinar si el id de la página empieza con "page-course-view"
$is_pagecourseview = false;
$attrs_body = explode(" ",$bodyattributes);
if ($attrs_body && isset($attrs_body[1])) {
    $attrs_id = explode("=",$attrs_body[1]);
    if ($attrs_id && isset($attrs_id[1])) {
        if (substr($attrs_id[1], 1, 16) == 'page-course-view') {
            $is_pagecourseview = true;
        }
    }
}

$url_actual= ' '.$_SERVER['REQUEST_URI'];
$exception = '/user/view.php';
if (strpos($url_actual, $exception)==1){
    $hasviewprofile = true;
}
else{
    $hasviewprofile = false;
}


// Para que se muestre bien en otras páginas con este layout
$hasviewcapability_course = false;
if ($courseid && $is_pagecourseview){
    $context = context_course::instance($COURSE->id);
    $userid = optional_param('id', $USER->id, PARAM_INT);
    // Buscamos usuarios por id y recuperamos el usuario[id] que nos interesa
    $users = user_get_users_by_id(array($userid));
    $user = $users[$userid];

    $viewing_user = $USER->id;

    // Usuarios solo pueden ver su perfil
    if (($viewing_user != $userid) && (has_capability('moodle/course:markcomplete', $context))==false) {
        $hasviewcapability_course = false;
    }
    elseif (($viewing_user == $userid) || has_capability('moodle/course:markcomplete', $context)){
        $hasviewcapability_course = true;
    }
    // Cargamos los profilefields al usuario
    profile_load_data($user);

    $mycourses = enrol_get_all_users_courses($user->id, true, null);
    $course = get_course($courseid, $clone = true);

    //Categoría del curso
    // $categories = coursecat::get_all();
    $categories = core_course_category::get_all();
    $coursecategory = '-';
    foreach ($categories as $category) {
        if ($category->path === '/' . $course->category) {
            $coursecategory = $category->name;
        }
    }

    // Nombres de las clases - grupos del usuario
    $classname = array();
    $groups = $DB->get_records_sql(' SELECT g.name
                                    FROM {groups} g
                                    JOIN {groups_members} gm on g.id = gm.groupid
                                    WHERE g.courseid = ?
                                    AND gm.userid = ?', [$courseid, $userid]
    );
    foreach ($groups as $group) {
        $classname[] = $group->name;
    }

    $context = context_course::instance($courseid);
    $students = get_role_users(5, $context);
    $teachers = get_role_users(3, $context);
    $managers = get_role_users(1, $context);

    foreach ($students as $student) {
        if ($student->id == $userid) {
            $userrole = get_string('defaultcoursestudent','core');
            break;
        }
    }

    foreach ($teachers as $teacher) {
        if ($teacher->id == $userid) {
            $userrole = get_string('defaultcourseteacher','core');;
        }
        $array_teachers[] = array(
            'name' => $teacher->firstname . ' ' . $teacher->lastname,
            'url' => new moodle_url("/user/view.php", ['id' => $teacher->id])
        );
    }

    foreach ($managers as $manager) {
        if ($manager->id == $USER->id) {
            $userrole = get_string('manager','core_role');
        }

        $array_managers[] = $manager->firstname . ' ' . $manager->lastname;
    }
    
    $user_activity['firstaccess']     = '';
    $user_activity['lastaccess']      = '';
    $user_activity['firstenrol']      = '';
    $user_activity['lastenrol']       = ''; // no se utiliza, código comentado
    $user_activity['coursecompleted'] = '';
    $user_activity['coursepassed']    = '';

    // Progreso
    $progresspercent = floor(\core_completion\progress::get_course_progress_percentage($course, $userid));
    
    $ccompletion = new completion_completion(array('userid' => $userid, 'course' => $courseid));

    // Solo mostramos la calificación en el curso cuando esté terminado
    $grade = false;
    $grade_str = '';
    
    if ($ccompletion->is_complete()) {
        // Calificación en el curso
        $grade = grade_get_course_grade($userid, $courseid);
        $grade_str = $grade->str_long_grade;
        // Fecha superación
        $user_activity['coursepassed'] = $grade->dategraded;
        // Fecha completado
        $user_activity['coursecompleted'] = $ccompletion->timecompleted;
    }

    // Secciones
    $course_sections = $DB->get_records('course_sections', array('course' => $courseid, 'visible' => 1), '', 'id,name,section');
    foreach ($course_sections as $section) {
        if ($section->name == '') $section->name = get_string('section').' '.$section->section;
        $sections[] = array('url' => new moodle_url("/course/view.php", ['id' => $courseid, 'section' => $section->section]), 'name' => $section->name);
    }
    
    // Actividades
    $completion = new \completion_info($course);
    $activities = $completion->get_activities();
    $type_activities = [];
    foreach ($activities as $activity) {
        $count = 1;
        if(isset($type_activities[$activity->modname])){
            $type_activities[$activity->modname] += 1;
        }
        else{
            $type_activities[$activity->modname] = 1;
        }

    }

    // Módulos (tipos de actividades)
    $array_modules = [];
    foreach ($type_activities as $activity => $count) {
        $array_modules[] = ['name' => get_string('modulename', $activity), 'cant' => $count];
    }
    
     // Fechas usuario
    $fechamatriculacion = get_user_tms($user, $course, 'enroldate');
    $fechaprimeracceso = $user_activity['firstaccess'] ? userdate($user_activity['firstaccess']) . " (" . format_time(time() - $user_activity['firstaccess']) . ")" : '-';
    $fechaultimoacceso = get_user_tms($user, $course, 'lastcourseaccess');
    $fechacursocompletado = $user_activity['coursecompleted'] ? userdate($user_activity['coursecompleted']) . " (" . format_time(time() - $user_activity['coursecompleted']) . ")" : '-';
    $fechacursosuperado = $user_activity['coursepassed'] ? userdate($user_activity['coursepassed']) : '-';
} else {
    $user = "";
    $course = "";
    $classname = array();
    $coursecategory = "";
    $array_teachers[] = array();
    $array_managers[] = array();
    $grade_str = "";
    $progresspercent = "";
    $sections = "";
    $array_modules[] = array();
    $user_activity = "";
    $fechamatriculacion = "";
    $fechaprimeracceso = "";
    $fechaultimoacceso = "";
    $fechacursocompletado = "";
    $fechacursosuperado = "";
}
if(empty($array_teachers)){
    $array_teachers=array(0);
}
if(empty($array_managers )){
    $array_managers =array(0);
}



/*PERFIL SITIO _____________________________*/
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
// Determinar si el id de la página empieza con "page-user-profile"
$is_pageprofileview = false;
$attrs_body = explode(" ",$bodyattributes);
if ($attrs_body && isset($attrs_body[1])) {
    if ($attrs_body[1] == 'id="page-user-profile"') {
        $is_pageprofileview = true;
    }
}


$viewing_user = $USER->id; 
$idUser = optional_param('id', $USER->id, PARAM_INT);
// Buscamos usuarios por id y recuperamos el usuario[id] que nos interesa
$users = user_get_users_by_id(array($idUser));
$user = $users[$idUser];
// Usuarios solo pueden ver su perfil
if ($viewing_user != $idUser && (has_capability('moodle/user:viewalldetails', $context))==false) {
    $hasviewcapability = false;
    $hasupdatecapability = false;

}
elseif ($viewing_user == $idUser || has_capability('moodle/user:viewalldetails', $context)){
    $hasviewcapability = true;
    if (has_capability('moodle/user:viewalldetails', $context)){
        $hasupdatecapability = true;
        $hasupdateusercapability = false;
    }
    else {
        $hasupdateusercapability = true;
        $hasupdatecapability = false;
    }
}
// PROFILEFIELDS
// Cargamos los profilefields al usuario
profile_load_data($user);


// Sacamos los enlaces de los profilefields tipo "file" (upload)
$file_profilefields = profile_get_user_fields_with_data_by_category($user->id);
$array_uploadedfiles = [];
foreach ($file_profilefields as $categoryid => $fields){
    foreach ($fields as $pf) {
        if ($pf->field->datatype == 'file'){
            $array_uploadedfiles[$pf->field->shortname] = $pf->display_data();
        }
    }
}

$profilefields = profile_get_custom_fields();
$ordered_profilefields = profile_user_record($idUser, $onlyinuserobject = true);
$array_ordered_profilefields = get_object_vars($ordered_profilefields);

$custom_profilefield = array();
$array_general = [];

foreach ($profilefields as $profilefield) {
    $str_name = $profilefield->name;
    $custom_profilefield[$profilefield->shortname] = $profilefield->name;

    // Posición "real" que ocupa ese profilefield en el listado de profilefields
    $position = array_search($profilefield->shortname, array_keys($array_ordered_profilefields));
    $profile = 'profile_field_' . $profilefield->shortname;

    // Comprobamos que el profilefield es visible para los usuarios
    if($profilefield->visible <> 0){
        // Añadimos el enlace a los que son de tipo file upload
        if ($profilefield->datatype == 'file'){
            $value = $array_uploadedfiles[$profilefield->shortname] == '' ? '-' : $array_uploadedfiles[$profilefield->shortname];
        } else {
            // Llenamos campos vacíos con '-'
            $value = empty($user->$profile) || $user->$profile == ' ' ? '-' : $user->$profile;
        }
    
        // Mostramos como fecha
        if ($profilefield->datatype == 'datetime' && $value != '-') {
            $value = date('d/m/Y', $value);
        }
        if ($profilefield->datatype == 'textarea' ) {
            $value = filter_var($value['text'], FILTER_SANITIZE_STRING);
            $value = empty($value) ? '-' : $value;
        }
             $array_general[$position] = ['str_name' => $str_name, 'value' => $value];
    }
}

/* Ordenamos los valores del array en función de la posición que les hemos dado antes (posición "real") y reestablecemos las claves para que se muestre correctamente. Si no, no mostrará nada en mustache */
ksort($array_general);
$array_general = array_values($array_general);


$user_record = $DB->get_record('user', array('id' => $idUser), '*', MUST_EXIST);
$context = context_user::instance($user_record->id, IGNORE_MISSING);

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

// Cursos en proceso y cursos finalizados
$mycourses = enrol_get_all_users_courses($user->id, true, null);
$cursosfinalizados = [];
$cursosenproceso = [];
$cursossolicitados = [];
$numfinish = 0;
$numproceso = 0;

foreach ($mycourses as $mycourse) {
    $finished = $DB->get_records_sql('SELECT timecompleted
                                        FROM {course_completions}
                                        WHERE timecompleted is not null
                                        AND userid = ? AND course = ?',
        [$user->id, $mycourse->id]
    );
    $ccontext = context_course::instance($mycourse->id);

    // URL a mostrar al hacer click en el curso
    $params = [
        'id' => $user->id,
        'course' => $mycourse->id,
    ];
    $url = new moodle_url('/user/view.php', $params);

    // Cursos finalizados
    if ($finished) {
        $numfinish++; // Número de cursos finalizados
        $cursosfinalizados[] = ['url' => $url, 'nombre' => $ccontext->get_context_name(false)];

        // Cursos en proceso
    } else {
        $numproceso++; // Número de cursos en proceso
        $cursosenproceso[] = ['url' => $url, 'nombre' => $ccontext->get_context_name(false)];
    }
}

// Cursos solicitados
$enrols = $DB->get_records_sql('SELECT courseid, e.id enrolid
    FROM {enrol_apply_applicationinfo} eaa
    JOIN {user_enrolments} ue on eaa.userenrolmentid = ue.id
    JOIN {enrol} e on ue.enrolid = e.id
    WHERE userid = ?', [$user->id]
);

$numsolicit = 0; // Número de cursos que se han solicitado
if ($enrols) {
    foreach ($enrols as $enrol) {
        $ccontext = context_course::instance($enrol->courseid);
        $params = [
            'id' => $enrol->enrolid,
        ];
        $url = new moodle_url('/enrol/apply/manage.php',
            $params);
        $numsolicit++;
        $cursossolicitados[] = ['url' => $url, 'nombre' => $ccontext->get_context_name(false)];
    }
}

// Datos de usuario: última ip conocida
if ($user->lastip) {
    $iplookupurl = new moodle_url('/iplookup/index.php',
        ['ip' => $user->lastip, 'user' => $user->id]);
    $ipstring = html_writer::link($iplookupurl, $user->lastip);
} else {
    $ipstring = get_string("none");
}

// Usuarios suspendidos TODO
if ($user->suspended != 0) {

    $suspension = $DB->get_records_sql('SELECT *
    FROM {user}
    WHERE id = ?', [$user->id]);
    
}

// Mostrar título de datos específicos si es mantainer, dotacion o instructor
$dotacionormantainer = false;

// Como los campos se añaden automáticamente, se rellenan los arrays antiguos
if (!empty($mantenimiento) || !empty($managetipe) || !empty($dotacion)) {
    $dotacionormantainer = true;
}


$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

// $acces_login = "inserver";

//Mostrar datos de usuario
if ($USER->id == $user->id) {
    $viewdetailsprofiletheme = true;
} else {
    if(has_capability('moodle/user:viewhiddendetails', $context)) {
        $viewdetailsprofiletheme = true;
    } else {
       $viewdetailsprofiletheme = false; 
    }
}

if(is_guest($contextg, $USER)){
    $isGuest = true;
}
else{
    $isGuest=false;
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
    // 'showeditmyprofile' => $showeditmyprofile,
    // 'showeditprofiles' => $showeditprofiles,

    'themename' => $themename,
    'sectiontitle' => $sectiontitle,
    'lang' => $lang,
    'hasupdatecapability' => $hasupdatecapability,
    'hasviewcapability' => has_capability('moodle/user:viewdetails', $context),
    'hasviewcapability' => $hasviewcapability,
    'hasupdateusercapability' => $hasupdateusercapability,
    'is_pageprofileview' => $is_pageprofileview,

    //Datos generales del usuario
    'image_url' => moodle_url::make_pluginfile_url(context_user::instance($user->id, IGNORE_MISSING)->id, 'user', 'icon', 0, '/', 'f1?t='.time()),
    'useridprofile' => $user->id == '' ? '-' : $user->id,
    'username' => $user->username == '' ? '-' : $user->username,
    'firstname' => $user->firstname == '' ? '-' : $user->firstname,
    'lastname' => $user->lastname == '' ? '-' : $user->lastname,
    'email' => $user->email == '' ? '-' : $user->email,
    'nacionalidad' => $user->country == '' ? '-' : get_string($user->country, 'core_countries'),
    'idioma' => $user->lang == '' ? '-' : $user->lang,
    'ciudad' => ($user->city == '') ? '-' : $user->city,
    'zonahoraria' => $user->timezone != 99 ? $user->timezone : 'Europa/París',
    'observaciones' => $user->description ? $user->description : '-',
    'userid' => $USER->id,

    // Profilefields que se añaden de manera automática
    'automaticgeneral' => $array_general,

    /* BLOQUES */
    // Formación interna (estado de los cursos)
    'cursosfinalizados' => empty($cursosfinalizados) ? ['url' => '', 'nombre' => '-'] : $cursosfinalizados,
    'numfinish' => $numfinish,
    'cursosenproceso' => empty($cursosenproceso) ? ['url' => '', 'nombre' => '-'] : $cursosenproceso,
    'numprocess' => $numproceso,
    'cursossolicitados' => empty($cursossolicitados) ? ['url' => '', 'nombre' => '-'] : $cursossolicitados,
    'numsolicit' => $numsolicit,

    // Fechas usuario (actividad de accesos)
    'fechacreacion' => userdate($user->timecreated) . "&nbsp; (" . format_time(time() - $user->timecreated) . ")",
    'fechaprimeracceso' => userdate($user->firstaccess) . "&nbsp; (" . format_time(time() - $user->firstaccess) . ")",
    'fechaultimoacceso' => userdate($user->lastaccess) . "&nbsp; (" . format_time(time() - $user->lastaccess) . ")",
    'ultimaip' => $ipstring,
    'fechasuspension' => $user->suspended == 1 ? get_string('suspended', 'core_auth') : get_string('active'),
    // 'exit_url' => (string) new moodle_url('/access/login_'. $acces_login .'/logout.php', array('sesskey' => sesskey())),

    /*PERFIL CURSO _____________________*/
    'userlongname' => empty($user) ? '' : $user->firstname . ' ' . $user->lastname,
    // Bloque que muestra los datos de los cursos
    'coursename' =>empty($course) ? '' : $course->fullname,
    'courseurl' => new moodle_url('/course/view.php', array('id' => $courseid)),
    'courseshortname' => empty($course) ? '' : $course->shortname,
    'category' => $coursecategory,
    'groups' => count($classname) ? $classname : array('-'),
    'userrole' => $userrole ?? '-',
    'courseteacher' => $array_teachers,
    'hascourseteacher' => count($array_teachers) ? true : false,
    'courseadmin' => count($array_managers) === 0 ? '-' : $array_managers,
    'shortdescription' => empty($course) ? '' : $course->summary,
    'grades' => $grade_str ? $grade_str : '-',
    'progress' => $progresspercent.' %',
    'section' => $sections,
    'modules' => $array_modules,
    // Fechas usuario actualizar mustache
    'fechamatriculacion' => $fechamatriculacion,
    'fechaprimeracceso_course' => $fechaprimeracceso,
    'fechaultimoacceso_course' => $fechaultimoacceso,
    'fechacursocompletado' => $fechacursocompletado,
    'fechacursosuperado' => $fechacursosuperado,
    /*Permisos mustache*/
    'hasviewcapability_course' => empty($user) ? '' : $hasviewcapability_course,
    'hasviewprofile_course' => $hasviewprofile,
    'isGuest' => $isGuest,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_itragsa/mypublic', $templatecontext);

