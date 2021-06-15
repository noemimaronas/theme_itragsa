<?php
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.
 
// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();                                                                                                
 
// A description shown in the admin theme selector.                                                                                 
$string['choosereadme'] = 'Theme Inserver Tragsa is a child theme of Boost. It adds the ability to upload background photos.';                
// The name of our plugin.                                                                                                          
$string['pluginname'] = 'Inserver Tragsa';                                                                                                    
// We need to include a lang string for each block region.                                                                          
$string['region-side-pre'] = 'Right';

$string['mypublic_userdata'] = 'Perfil de usuario';
$string['mypublic_usergeneraldata'] = 'Datos generales';
$string['mypublic_userid'] = 'Id de usuario';
$string['mypublic_nationality'] = 'Nacionalidad';
$string['mypublic_zonahoraria'] = 'Zona horaria';
$string['mypublic_observations'] = 'Descripción';
$string['mypublic_fi_finalizados'] = 'Cursos finalizados';
$string['mypublic_fi_proceso'] = 'Cursos en proceso';
$string['mypublic_fi_solicitados'] = 'Cursos solicitados';
$string['mypublic_vermas'] = 'Ver más';
$string['mypublic_vermenos'] = 'Ver menos';
$string['mypublic_aa_creacion'] = 'Creación de usuario';
$string['mypublic_aa_suspension'] = 'Estado del usuario';

$string['standard_userdata'] = 'Datos del usuario en el curso';
$string['standard_profile'] = 'Ver perfil general';
$string['standard_status'] = 'Estado del curso';
$string['standard_coursename'] = 'Nombre del curso';
$string['standard_courseshortname'] = 'Nombre corto';
$string['standard_category'] = 'Categoría';
$string['standard_group'] = 'Grupo';
$string['standard_userrole'] = 'Rol del usuario';
$string['standard_courseteacher'] = 'Profesor del curso';
$string['standard_courseinstructor'] = 'Instructor del curso';
$string['standard_shortdescription'] = 'Descripción del curso';
$string['standard_grades'] = 'Calificación';
$string['standard_progress'] = 'Progreso';
$string['standard_section'] = 'Secciones';
$string['standard_modules'] = 'Módulos';
$string['standard_courseactivity'] = 'Actividad del curso';
$string['standard_enroldate'] = 'Fecha de matriculación';
$string['standard_superacion'] = 'Fecha de superación';
$string['standard_finalizacion'] = 'Fecha de finalización';
$string['rol_student'] = 'Estudiante';
$string['rol_teacher'] = 'Profesor';
$string['rol_instructor'] = 'Instructor';
$string['rol_manager'] = 'Gestor';
$string['modulename'] = 'Nombre del módulo';


// Other
$string['configtitle'] = 'Inserver Theme ajustes';   
$string['inserver'] = 'Inserver ajustes';                                                                                   
$string['generalsettings'] = 'Ajustes generales';                                                                                                
$string['preset'] = 'Theme preset';                                                 
$string['preset_desc'] = 'Elige un preset para cambiar el aspecto del tema.';
$string['presetfiles'] = 'Additional theme preset files';                                                                                                        
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
$string['brandcolor'] = 'Brand colour';                                                                                                                                                                                     
$string['brandcolor_desc'] = 'The accent colour.';
$string['rawscss'] = 'Raw SCSS';                                                                                              
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';                                                                                    
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['advancedsettings'] = 'Advanced settings';
$string['numslides'] = 'Number of slides';
$string['numslides_desc'] = 'Number of slides in the carousel. Save the changes in order to see the settings of each slide.';

// Dash

$string['dash_quote'] = 'Hola ';
$string['dash_description'] = 'E-learning para todos';
$string['course_quickstart'] = 'Cursos de inicio rápido';
$string['course_enroll'] = 'Cursos con solicitud de matriculación';
$string['course_stripepayment'] = 'Comprar cursos';

$string['signup_title'] = 'Introducción FORMULARIO AUTOREGISTRO:';
$string['signup_text'] = 'El Instituto de las Mujeres en el marco del programa “Desafío Mujer Rural” (cofinanciado por el Fondo Social Europeo), ha desarrollado una Plataforma de Formación que integra un Programa Formativo sobre EMPRENDIMIENTO PARA MUJERES DEL ÁMBITO RURAL.<br>El formulario que se encontrará en la siguiente página supone su registro en la plataforma de formación “Desafío Mujer Rural” y, por tanto, el acceso a la inscripción en los cursos disponibles.<br>El formulario consta de varias cuestiones con diferentes opciones de respuesta. Todas ellas deben responderse de forma obligatoria para registrarse en la plataforma y acceder, posteriormente a los cursos.<br>Este, sólo se puede enviar una vez, por lo que es necesario que compruebe que ha respondido a todas las cuestiones correctamente. Cuando lo finalice pulse “enviar”. Si el formulario no se cumplimenta adecuadamente o no se envía el formulario, no aparecerá registrada. <br>Tras enviar el formulario, le llegará un correo automático de haber recibido correctamente su registro en la plataforma. <br>Posteriormente, y una vez valorado que se cumplen los requisitos de las destinatarias del Programa: ser mujer y tener una idea de negocio para desarrollar en el ámbito rural o ser empresaria con una empresa creada en el medio rural, le enviaremos un e-mail de confirmación sobre su registro en la plataforma. Entonces ya podrá inscribirse en los cursos y actividades que convoque el Programa Formativo Desafío Mujer Rural.<br>Le damos las gracias por el tiempo que va a dedicarle y por su participación.';