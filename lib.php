<?php
 
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.
 
// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();
 
// We will add callbacks here as we add features to our theme.


require_once($CFG->libdir.'/grouplib.php');

function theme_itragsa_get_main_scss_content($theme) {

    global $CFG;


    $boost .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');


    $post = file_get_contents($CFG->dirroot . '/theme/itragsa/scss/post.scss'); 

    return 
        $boost  . "\n" .
        $post;

}



