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

$string['mypublic_userdata'] = 'User Profile';
$string['mypublic_usergeneraldata'] = 'User information';
$string['mypublic_userid'] = 'User ID';
$string['mypublic_nationality'] = 'Nationality';
$string['mypublic_zonahoraria'] = 'Time zone';
$string['mypublic_observations'] = 'Description';
$string['mypublic_fi_finalizados'] = 'Courses completed';
$string['mypublic_fi_proceso'] = 'Ongoing courses';
$string['mypublic_fi_solicitados'] = 'Requested courses';
$string['mypublic_vermas'] = 'See more';
$string['mypublic_vermenos'] = 'See less';
$string['mypublic_aa_creacion'] = 'User creation date';
$string['mypublic_aa_suspension'] = 'User status';

$string['standard_userdata'] = 'User course data';
$string['standard_profile'] = 'View profile';
$string['standard_status'] = 'Course status';
$string['standard_coursename'] = 'Course name';
$string['standard_courseshortname'] = 'Short name';
$string['standard_category'] = 'Category';
$string['standard_group'] = 'Group';
$string['standard_userrole'] = 'User role';
$string['standard_courseteacher'] = 'Course teacher';
$string['standard_courseinstructor'] = 'Course instructor';
$string['standard_shortdescription'] = 'Course description';
$string['standard_grades'] = 'Grade';
$string['standard_progress'] = 'Progresss';
$string['standard_section'] = 'Sections';
$string['standard_modules'] = 'Modules';
$string['standard_courseactivity'] = 'Course activity';
$string['standard_enroldate'] = 'Enrollment date';
$string['standard_superacion'] = 'Overcoming date';
$string['standard_finalizacion'] = 'Completion date';
$string['rol_student'] = 'Student';
$string['rol_teacher'] = 'Teacher';
$string['rol_instructor'] = 'Instructor';
$string['rol_manager'] = 'Manager';
$string['modulename'] = 'Module name';

// Other
$string['configtitle'] = 'Inserver Theme settings';   
$string['inserver'] = 'Inserver settings';
$string['generalsettings'] = 'General settings';                                                                                                 
$string['preset'] = 'Theme preset';                                                  
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
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

$string['dash_quote'] = 'Hello ';
$string['dash_description'] = 'E-learning for Everyone';
$string['course_quickstart'] = 'Quick start courses';
$string['course_enroll'] = 'Application enrollment courses';
$string['course_stripepayment'] = 'Buy courses';

$string['signup_title'] = 'Introducción FORMULARIO AUTOREGISTRO:';
$string['signup_text'] = 'El Instituto de las Mujeres en el marco del programa “Desafío Mujer Rural” (cofinanciado por el Fondo Social Europeo), ha desarrollado una Plataforma de Formación que integra un Programa Formativo sobre EMPRENDIMIENTO PARA MUJERES DEL ÁMBITO RURAL.<br>El formulario que se encontrará en la siguiente página supone su registro en la plataforma de formación “Desafío Mujer Rural” y, por tanto, el acceso a la inscripción en los cursos disponibles.<br>El formulario consta de varias cuestiones con diferentes opciones de respuesta. Todas ellas deben responderse de forma obligatoria para registrarse en la plataforma y acceder, posteriormente a los cursos.<br>Este, sólo se puede enviar una vez, por lo que es necesario que compruebe que ha respondido a todas las cuestiones correctamente. Cuando lo finalice pulse “enviar”. Si el formulario no se cumplimenta adecuadamente o no se envía el formulario, no aparecerá registrada. <br>Tras enviar el formulario, le llegará un correo automático de haber recibido correctamente su registro en la plataforma. <br>Posteriormente, y una vez valorado que se cumplen los requisitos de las destinatarias del Programa: ser mujer y tener una idea de negocio para desarrollar en el ámbito rural o ser empresaria con una empresa creada en el medio rural, le enviaremos un e-mail de confirmación sobre su registro en la plataforma. Entonces ya podrá inscribirse en los cursos y actividades que convoque el Programa Formativo Desafío Mujer Rural.<br>Le damos las gracias por el tiempo que va a dedicarle y por su participación.';