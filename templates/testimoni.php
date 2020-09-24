<?php

global $wpdb;

$table  = $wpdb->prefix . 'testimonial';
$result = $wpdb->get_row( "SELECT * FROM $table WHERE blog_id = 'get_current_blog_id()' ORDER BY RAND() LIMIT 1 " );

$html = '<div>
                    
        <blockquote>
            <h6>'.$result->name.' ('.$result->email.')</h6><br>
            <span>'.$result->testimoni.'</span>
        </blockquote>
    </div>'; 

echo $html;

