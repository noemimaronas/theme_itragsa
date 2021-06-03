<?php
 
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.
 
// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();
 
// We will add callbacks here as we add features to our theme.



/**
 * Transforma segundos a horas:minutos:segundos
 *
 * @param int $ss segundos
 *
 * @return string
 */

// Standard corregido

function get_crs_actvts($course)
{
    $course_activities = get_activities($course);
    $activities        = [];
    $zoomExist = get_exst_zoom();
    
    if ($zoomExist !== false){
        foreach ($course_activities as $course_activity) {
            if ($course_activity->modname === 'scorm' || $course_activity->modname === 'quiz' ||
                $course_activity->modname === 'lesson' || $course_activity->modname === 'zoom'
                || $course_activity->modname === 'assign') {
                $activities[] = $course_activity;
            }

        }
    }
    else{
        foreach ($course_activities as $course_activity) {
            if ($course_activity->modname === 'scorm' || $course_activity->modname === 'quiz' ||
                $course_activity->modname === 'lesson' || $course_activity->modname === 'assign'){
                $activities[] = $course_activity;
            }
            // echo '<pre>';
            //     var_dump($course_activity->modname);
            // echo '<br>';

            //     var_dump($course_activity->instance);
            //     echo '</pre>';
        }
    }
    
    return $activities;
}

/**
 * Devuelve si existe mod_zoom
 *
 *
 * @return boolean
 * @throws \dml_exception
 */
function get_exst_zoom(){
    global $DB;
    
    $zoomExist = $DB->get_records_sql("
                    SELECT 
                        name
                    FROM
                        {modules}
                    WHERE
                        name like 'zoom'
                    AND
                        EXISTS (SELECT NULL)");
    
    if (!empty($zoomExist)){
        $zoom = true;
    }
    else{
        $zoom = false; 
    }

    return $zoom;
}


/**
 * Obtiene el tiempo total de zoom
 *
 * @param $courseid
 * @param $userid
 *
 * @return \stdClass
 * @throws \dml_exception
 */

function get_zoom_ttl_time($courseid, $userid){
    global $DB;
    $sqldurationuser = 0;
    $zoomtimes = 0;
    $zoomExist = get_exst_zoom();

    if ($zoomExist !== false){

        $sqlzoom = $DB->get_records_sql('
                    SELECT
                        meeting_id
                    FROM
                        {zoom}
                    WHERE
                        course = :courseid', ['courseid'=>$courseid]);

        foreach ($sqlzoom as $meeting_id){
            $meeting = $meeting_id->meeting_id;
            $sqlmeetingdetails =  $DB->get_records_sql('
                                    SELECT
                                        id
                                    FROM
                                        {zoom_meeting_details}
                                    WHERE
                                    meeting_id = :meeting_id',['meeting_id'=>$meeting]);

            foreach ($sqlmeetingdetails as $idmeetingdetails){
                $sqldurationuser =  $DB->get_records_sql('
                                        SELECT
                                            duration
                                        FROM
                                            {zoom_meeting_participants}
                                        WHERE
                                            detailsid = :meetingdetails_id
                                        AND
                                            userid = :userid',['meetingdetails_id'=>$idmeetingdetails->id, 'userid'=>$userid]);
                foreach ($sqldurationuser as $duration){
                    $zoomtimes = $zoomtimes+$duration->duration;
                }
            }
        }
        return secs2human($zoomtimes);
    }
    else{
        $zoomtimes = '-';
        return $zoomtimes;
    }
}

/**
 * Obtiene los tiempos de zoom
 *
 * @param $courseid
 * @param $userid
 *
 * @return \stdClass
 * @throws \dml_exception
 */
function get_zoom_tms($zoomid, $userid)
{
    global $DB;
    $zoomtimes = new stdClass();
    $duration = 0;
    $start = 0;
    $sqldurationuser = 0;
    $zoomExist = get_exst_zoom();

    if ($zoomExist !== false){

        $sqlzoom = $DB->get_records_sql('
                    SELECT
                        meeting_id
                    FROM
                        {zoom}
                    WHERE
                        id = :zoomid', ['zoomid'=>$zoomid]);

        foreach ($sqlzoom as $meeting_id){
            $meeting = $meeting_id->meeting_id;
            $sqlmeetingdetails =  $DB->get_records_sql('
                                    SELECT
                                        id
                                    FROM
                                        {zoom_meeting_details}
                                    WHERE
                                    meeting_id = :meeting_id',['meeting_id'=>$meeting]);

            if(!empty($sqlmeetingdetails)){
                foreach ($sqlmeetingdetails as $idmeetingdetails){
                    $sqlusertimes =  $DB->get_records_sql('
                                            SELECT
                                                duration, join_time, leave_time
                                            FROM
                                                {zoom_meeting_participants}
                                            WHERE
                                                detailsid = :meetingdetails_id
                                            AND
                                                userid = :userid',['meetingdetails_id'=>$idmeetingdetails->id, 'userid'=>$userid]);
                    foreach ($sqlusertimes as $times){
                        $duration = $duration + $times->duration;
                        $start = ($start == 0 || $times->join_time < $start) ? $times->join_time : $start;
                        $zoomtimes->start = intval($start);
                        $zoomtimes->finish = intval($times->leave_time);
                        $zoomtimes->duration[0] = secs2human(intval($duration));
                    }
                }
            }
        }
   
        return $zoomtimes;
    }
    else{
        $zoomtimes = '-';
        return $zoomtimes;
    }
}

 
/**
 * Obtiene el tiempo de los quiz
 *
 * @param $quizid
 * @param $userid
 *
 * @return \stdClass
 * @throws \dml_exception
 */
function get_qz_tms($quizid, $userid)
{
    global $DB;
    $runtime = new stdClass();

    $sql = "SELECT
				timestart, timefinish
            FROM
            	{quiz_attempts}
            WHERE
            	quiz = :quizid
            AND userid = :userid
            AND state='finished'
          	ORDER BY timestart";

    $quizattempts = $DB->get_records_sql($sql, compact('quizid', 'userid'));

    if ($quizattempts) {
        $runtime->start    = current($quizattempts)->timestart;
        $runtime->duration = 0;
        foreach ($quizattempts as $quizattempt) {
            $runtime->finish   = $quizattempt->timefinish;
            $runtime->duration += ($quizattempt->timefinish - $quizattempt->timestart);
        }

        $runtime->duration = [secs2human($runtime->duration)];
    }
    
    return $runtime;
}

/**
 * Obtiene el tiempo de los assign/tareas
 *
 * @param $scormid
 * @param $userid
 *
 * @return \stdClass
 * @throws \dml_exception
 */
 function get_assgn_tms($assignment, $userid){
    global $DB;
    $timefinishassign = new stdClass();
    $params = compact('userid', 'assignment');
    $timeassign = $DB->get_record('assign_submission', $params);

    if($timeassign){
        if ($timeassign->status == 'submitted'){
            $timefinishassign->finish = $timeassign->timemodified;
        }
    }
    return $timefinishassign;
 }


/**
 * Obtiene el tiempo de los scorm
 *
 * @param $scormid
 * @param $userid
 *
 * @return \stdClass
 * @throws \dml_exception
 */
function get_scrm_tms($scormid, $userid)
{
    global $DB;

    $timedata = new stdClass();

    $params = compact('userid', 'scormid');
    $tracks = $DB->get_records('scorm_scoes_track', $params, 'timemodified ASC');
    if ($tracks) {
        $tracks = array_values($tracks);
    }

    if ($tracks) {
        $timedata->start = $tracks[0]->timemodified;
    }

    $durations = [];
    foreach ($tracks as $track) {
        if ($track->element === 'cmi.core.total_time') {
            $durations[] = $track->value;
            $timedata->finish = $track->timemodified;
        }

    }

    $timedata->duration = $durations;

    
    return $timedata;
}

/**
 * Obtiene el tiempo de las lecciones.
 *
 * @param $lessonid
 * @param $userid
 * @return int
 * @throws dml_exception
 */
function get_lssn_tms($lessonid, $userid)
{
    global $DB;

    $params = compact('userid', 'lessonid');
    $times  = $DB->get_records('lesson_timer', $params);
    $time   = 0;

    if ($times) {
        foreach ($times as $time) {
            $time = (int)$time->lessontime;
        }
    }

    return $time;
}

/**
 * Crea un array con los datos de las actividades para cada usuario para
 * mostrarlo en la plantilla
 *
 * @param $user
 * @param $course
 *
 * @return array
 * @throws \dml_exception
 * @throws \moodle_exception
 */
function get_user_actvts($user, $course)
{
    global $DB;
    $array_activities = [];
    $activities       = get_crs_actvts($course);

    foreach ($activities as $activity) {
        $finalgrade = $DB->get_record_sql(
                'SELECT gg.rawgrade
				FROM
    				{grade_grades} gg
        		JOIN
        			{grade_items} gi ON gg.itemid = gi.id
				WHERE
    				gi.iteminstance = :iteminstance
                AND 
                    gg.userid = :userid
                AND
                    gi.itemmodule = :modulename'
                ,
                ['iteminstance' => $activity->instance, 'userid' => $user->id, 'modulename' => $activity->modname]);

        
        $funcname = 'get_' . $activity->modname . '_times';
        $times    = $funcname($activity->instance, $user->id);

        $activitygrade = $finalgrade->rawgrade ?? '-';
        
        if($activity->modname == 'assign'){
            $array_activities[] = [
                'activityname'       => $activity->name,
                'activityurl'        => get_actvt_url($activity),
                'activitygrade'      => round($activitygrade),
                'activityfinishtime' => isset($times->finish) && !empty($times->finish) ? userdate($times->finish) : '-'
            ];
        }
        else{
            $array_activities[] = [
                'activityname'       => $activity->name,
                'activityurl'        => get_actvt_url($activity),
                'activitygrade'      => round($activitygrade),
                'activitystarttime'  => isset($times->start) && !empty($times->start) ? userdate($times->start) : '-',
                'activitytime'       => $times->duration[0] ?? '-',
                'activityfinishtime' => isset($times->finish) && !empty($times->finish) ? userdate($times->finish) : '-'
                ];
        }
    }

 
    return $array_activities;
}

/**
 * Obtiene el tiempo total de todos los quiz de un curso para un usuario
 *
 * @param $course
 * @param $user
 *
 * @return string
 * @throws \dml_exception
 */
function get_qz_ttl_time($course, $user)
{
    $activities    = get_crs_actvts($course);
    $total_seconds = 0;

    foreach ($activities as $activity) {
        if ($activity->modname === 'quiz') {
            $times         = get_qz_tms($activity->instance, $user->id);
            $td            = $times->duration[0] ?? 0; // tiempo que se ha tardado en hacer la actividad
            $seconds       = strtotime("1970-01-01 $td UTC");
            $total_seconds += $seconds;
        }
    }

    return secs2human($total_seconds);
}

/**
 * Obtiene el tiempo de todos los scorms de un usuario en un curso
 *
 * @param $course
 * @param $user
 *
 * @return string
 * @throws \dml_exception
 */
function get_scrm_ttl_time($course, $user)
{
    $activities    = get_crs_actvts($course);
    $total_seconds = 0;

    foreach ($activities as $activity) {
        if ($activity->modname === 'scorm') {
            $times         = get_scrm_tms($activity->instance, $user->id);
            $td            = $times->duration[0] ?? 0;        // tiempo que se ha tardado en hacer la actividad
            $seconds       = strtotime("1970-01-01 $td UTC"); // Pasamos el string a milisegundos
            $total_seconds += $seconds;
        }
    }

    return secs2human($total_seconds);
}

/**
 * Obtiene el tiempo de las actividades de tipo lección (lesson).
 *
 * @param $course
 * @param $user
 * @return string
 * @throws dml_exception
 */
function get_lssn_ttl_time($course, $user)
{
    $activities = get_crs_actvts($course);
    $seconds    = 0;

    foreach ($activities as $activity) {
        if ($activity->modname === 'lesson') {
            $times   = get_lssn_tms($activity->instance, $user->id);
            $seconds = $times;
        }
    }

    return secs2human($seconds);

}

/**
 * Obtiene el tiempo total de todas las actividades de un usuario para un curso
 *
 * @param $course
 * @param $user
 *
 * @return string
 * @throws \dml_exception
 */
// function get_activities_total_time($course, $user)
// {
//     $total_time_quiz   = strtotime('1970-01-01' . get_qz_ttl_time($course, $user) . 'UTC');
//     $total_time_scorm  = strtotime('1970-01-01' . get_scrm_ttl_time($course, $user) . 'UTC');
//     $total_time_lesson = strtotime('1970-01-01' . get_lssn_ttl_time($course, $user) . 'UTC');
//     if (get_exst_zoom() !== false){
//         $total_time_zoom   = strtotime('1970-01-01' . get_zoom_ttl_time($course->id, $user->id) . 'UTC');
//     }
//     else{
//         $total_time_zoom = 0;
//     }

//     return secs2human($total_time_quiz + $total_time_scorm + $total_time_lesson + $total_time_zoom);
// }

/**
 * Obtiene y devuelve la url de una actividad
 *
 * @param $activity
 *
 * @return \moodle_url
 * @throws \moodle_exception
 */
function get_actvt_url($activity)
{
    return new moodle_url("/mod/$activity->modname/view.php", ['id' => $activity->id]);
}

/** ----------------------------
 *          CURSO
 * ------------------------------
 */

/**
 * Calcula el porcentaje de completado del curso en base a las actividades.
 *
 * @param $course
 * @param $user
 *
 * @return float
 */
function get_course_prgrss($course, $user)
{
    list($num_user_activities, $num_total_activities) = get_num_cmpltd_activities($course, $user);

    return round($num_user_activities === 0 ? 0 : ($num_user_activities / $num_total_activities) * 100, 2);
}

/**
 * Obtiene la fecha de matriculación y la de última conexión.
 * Es necesario pasarle el parámetro que queremos obtener (enroldate o
 * lastcourseaccess)
 *
 * @param        $user
 * @param        $course
 * @param string $field tiempo que queremos sacar (enroldate o lastcourseaccess)
 *
 * @return mixed
 * @throws \dml_exception
 */
function get_user_tms($user, $course, $field)
{
    global $DB;
    $sql = /** @lang text */
            '
		SELECT
			u.id, l.timeaccess AS lastcourseaccess, ue.timecreated AS enroldate
		FROM
			{user} u
		LEFT OUTER JOIN
			{user_lastaccess} l ON l.userid = u.id
		AND
			l.courseid = :courseid
		JOIN
			{user_enrolments} ue ON ue.userid = :userid
		JOIN
			{enrol} e ON e.id = ue.enrolid
		AND
			e.courseid = :courseid1
        WHERE
        u.id = :userid1';

    $user_times = $DB->get_records_sql($sql, [
            'courseid'  => $course->id,
            'userid'    => $user->id,
            'courseid1' => $course->id,
            'userid1'   => $user->id

    ]);
    $user_time  = $user_times[$user->id]->$field;

    return $user_time !== null ? userdate($user_time) : '-';
}

/**
 * Obtiene todos los grupos de un curso
 *
 * @param $course
 *
 * @return array
 */
function get_all_course_grps($course)
{
    $groups       = groups_get_all_groups($course->id);
    $array_groups = [];

    foreach ($groups as $group) {
        $array_groups[] = ['id' => $group->id, 'name' => $group->name];
    }

    return $array_groups;
}

/** ----------------------------
 *          USUARIO
 * ------------------------------
 */

/**
 * Devuelve progreso de un/a estudiante.
 *
 * @param stdClass $course Course
 * @param int $userid User id
 *
 * @return array
 * @throws \dml_exception
 */
function get_prgrss($course, $userid)
{
    global $DB;
    $progress = $DB->get_records_sql('
        SELECT
            cmc.coursemoduleid activityid, cmc.id, cmc.completionstate, cmc.timemodified
        FROM
            {course_modules} cm
            JOIN {modules} m ON m.id = cm.module
            JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
        WHERE
            cm.course = :courseid AND cmc.userid = :userid', ['courseid' => $course->id, 'userid' => $userid]
    );

    return $progress;
}

/**
 * Obtiene los campos personalizados de los usuarios
 *
 * @param $user
 *
 * @return array
 */
function get_user_profileflds($user)
{
    $profilefields      = profile_get_user_fields_with_data($user->id);
    $array_profilefield = [];

    foreach ($profilefields as $profilefield) {
        $array_profilefield[] = [
            'profilefieldvalue' =>  filter_var($profilefield->data, FILTER_SANITIZE_STRING)

        ];
    }
    return $array_profilefield;
}

function get_hdrs_table(){
    $fullprofilefields      = profile_get_user_fields_with_data(2);
    $array_profilefield_header = [];
    $profilefields_custom = profile_get_custom_fields();
    $i                   = 0;

    
    foreach ($fullprofilefields as $profilefield_header) {
        $custom_field_name = substr($profilefield_header->inputname, 14);
            foreach ($profilefields_custom as $profilefield_name){
                $str_name = $profilefield_name->shortname;
                $pfl_name = $profilefield_name->name;
            if ($str_name == $custom_field_name){
                $array_profilefield_header[] = [
                    'thprofilefieldname' => filter_var($pfl_name, FILTER_SANITIZE_SPECIAL_CHARS),
                    'numprofilefield'    => ++$i + 2, // filtro por columnas

                ];
                
            }
            
        }
    }

    return $array_profilefield_header;

}

/**
 * Obtiene el número de actividades completadas de un usuario (que tienen
 * tiempo de finalización) respecto del total
 *
 * @param $course
 * @param $user
 *
 * @return array
 */
function get_num_cmpltd_activities($course, $user)
{
    $activities          = get_crs_actvts($course);
    $num_activities      = count($activities);
    $num_user_activities = 0;

    foreach ($activities as $activity) {
        // Obtenemos el tiempo del usuario en cada actividad
            $funcname = 'get_' . $activity->modname . '_times';
            $times    = $funcname($activity->instance, $user->id);

            // Si tiene tiempo de finalización se cuenta como completada
            $num_user_activities += isset($times->finish) ? 1 : 0;
    }

    return [$num_user_activities, $num_activities];
}

/**
 * Obtiene todos los grupos en los que está un usuario.
 * Devuelve '-' si el grupo está vacío. Si tiene más de un grupo devuelve una concatenación de los mismos.
 *
 * @param $course
 * @param $user
 *
 * @return string
 */
function get_user_grps($course, $user)
{
    $user_groups = groups_get_user_grps($course->id, $user->id);

    if (empty($user_groups[0])) {
        return '-';
    }

    $user_group = '';
    $groups     = groups_get_all_groups($course->id);

    foreach ($groups as $group) {
        foreach ($user_groups[0] as $group_id) {
            if ((int)$group->id === (int)$group_id) {
                $user_group .= $group->name . ' ';
            }
        }
    }

    return $user_group;
}

/**
 * Genera todos los datos para enviar a la plantilla mustache
 *
 * @param   $course
 *
 * @return array
 * @throws \dml_exception
 * @throws \moodle_exception
 */
function get_tbl_data($course)
{
    $array_user = [];
    $users      =
            get_enrolled_users(context_course::instance($course->id), 'moodle/grade:view', 0, 'u.*', 'u.lastname', 0, 0, true);
    foreach ($users as $user) {
        $array_user[] = [
                'name'                => $user->firstname . ' ' . $user->lastname,
                'userurl'             => new moodle_url('/user/view.php', ['id' => $user->id, 'course' => $course->id]),
                'email'               => $user->email,
                'usercreation'        => userdate(intval($user->timecreated)),
                'profilefield'        => get_user_profileflds($user),
                'groups'              => get_user_grps($course, $user),
                'startdate'           => userdate($course->startdate),
                'enroldate'           => get_user_tms($user, $course, 'enroldate'),
                'lastaccess'          => get_user_tms($user, $course, 'lastcourseaccess'),
                'activity'            => get_user_actvts($user, $course),
                'totaltimequiz'       => get_qz_ttl_time($course, $user),
                'totaltimescorm'      => get_scrm_ttl_time($course, $user),
                'totaltimelesson'     => get_lssn_ttl_time($course, $user),
                'totaltimezoom'       => get_zoom_ttl_time($course->id, $user->id),
                'totaltime'           => get_activities_total_time($course, $user),
                'activitiescompleted' => implode('/', get_num_cmpltd_activities($course, $user)),
                'progress'            => get_course_prgrss($course, $user) . '%'
        ];
    }

    return $array_user;
}

/**
 * Devuelve el nombre de los campos personalizados para mostrarlo en la
 * plantilla mustache
 *
 * @return array
 */
function get_prflfields_name()
{
    $array_profilefields = [];
    $profilefields       = profile_get_custom_fields();
    $i                   = 0;
    foreach ($profilefields as $profilefield) {
        $array_profilefields[] = [
                'thprofilefieldname' => filter_var($profilefield->name, FILTER_SANITIZE_SPECIAL_CHARS),
                'numprofilefield'    => ++$i + 2, // filtro por columnas
        ];
    }


    return $array_profilefields;
}

/**
 * Devuelve el nombre de las actividades de ese curso para mostrarlo en la
 * plantilla mustache
 *
 * @param $course
 *
 * @return array
 * @throws \moodle_exception
 */
function get_actvt_names($course)
{
    $array_activity_names = [];

    $activities = get_crs_actvts($course);
    foreach ($activities as $activity) {
        $hidden = true;
        if($activity->modname == 'assign'){
            $hidden = false;

        }
        $array_activity_names[] = [
                'thactivityname' => filter_var($activity->name, FILTER_SANITIZE_SPECIAL_CHARS),
                'thactivitydisplay' => $hidden,
                'thactivityurl'  => get_actvt_url($activity)
        ];
    }

    return $array_activity_names;
}

// Fin standard corregido