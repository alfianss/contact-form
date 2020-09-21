<?php
/*
Plugin Name: Contact Form Plugin
Plugin URI: http://example.com
Description: Simple WordPress Contact Form
Version: 1.0
Author: Alfian SS
Author URI: http://w3guy.com
*/

// Database
add_option( "con_db_version", "1.0" );

global $con_db_version;
$con_db_version = '1.0';

function con_install() {
	global $wpdb;
	global $con_db_version;

	$table_name = $wpdb->prefix . 'testimonial';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,		
		name varchar(50) DEFAULT '' NOT NULL,
        email varchar(50) DEFAULT '' NOT NULL,
        phone_number varchar(15) DEFAULT '' NOT NULL,
        testimoni text DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

    // add_option( 'con_db_version', $con_db_version );
    update_option( 'con_db_version', $con_db_version );
}

function con_install_data() {
	global $wpdb;
	
	$welcome_name = 'Mr. WordPress';
	$welcome_text = 'Congratulations, you just completed the installation!';
	
	$table_name = $wpdb->prefix . 'testimonial';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time( 'mysql' ), 
			'name' => $welcome_name, 
			'text' => $welcome_text, 
		) 
	);
}

register_activation_hook( __FILE__, 'con_install' );
register_activation_hook( __FILE__, 'con_install_data' );

function form_contact_update_db_check() {
    global $con_db_version;
    if ( get_site_option( 'con_db_version' ) != $con_db_version ) {
        con_install();
    }
}
add_action( 'plugins_loaded', 'form_contact_update_db_check' );

// Form
function html_form_code() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>';
	echo 'Your Name (required) <br/>';
	echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Your Email (required) <br/>';
	echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Phone Number <br/>';
	echo '<input type="text" name="cf-phone" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["cf-phone"] ) ? esc_attr( $_POST["cf-phone"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Testimonial<br/>';
	echo '<textarea rows="10" cols="35" name="cf-testimonial">' . ( isset( $_POST["cf-testimonial"] ) ? esc_attr( $_POST["cf-testimonial"] ) : '' ) . '</textarea>';
	echo '</p>';
	echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
	echo '</form>';
}

function save_testimonial() {

	// if the submit button is clicked, send the email
	if ( isset( $_POST['cf-submitted'] ) ) {

		// sanitize form values
		$name        = sanitize_text_field( $_POST["cf-name"] );
		$email       = sanitize_email( $_POST["cf-email"] );
		$phone       = sanitize_text_field( $_POST["cf-phone"] );
		$testimonial = esc_textarea( $_POST["cf-testimonial"] );

        $insertdb = $wpdb->insert(
            $wpdb->prefix . 'testimonial', 
            array( 
                'name' => $name, 
                'email' => $email,
                'phone_number' => $phone,
                'testimoni' => $testimonial, 
            ), 
            array( 
                '%s', 
                '%s', 
                '%s',
                '%s',
            ) 
        );
        
		if ( $insertdb ) {
			echo '<div>';
			echo '<p>Thanks for contacting me, expect a response soon.</p>';
			echo '</div>';
		} else {
			echo 'An unexpected error occurred';
		}
	}
}

// Admin Menu
add_action( 'admin_menu', 'my_admin_menu' );


function myplugin_admin_page(){
	echo "<div class='wrap'><h2>Welcome To My Plugin</h2></div>";
	
	global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}testimonial", OBJECT );

	echo "<table border='1'>
			<thead>
				<th>No</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone Number</th>
				<th>Testimony</th>
				<th>Action</th>
			<thead>";
	$no = 1; 
	foreach($results as $tm) {
		echo "<td>".$no."</td>
			  <td>".$tm->name."</td>
			  <td>".$tm->email."</td>
			  <td>".$tm->phone_number."</td>
			  <td>".$tm->testimoni."</td>
			  <td><a>Delete</a></td>";
		$no++;
	}

	echo "</table>";
}

function my_admin_menu() {
	add_menu_page( 'Contact Form Menu', 'Contact Form', 'manage_options', 'contact-form/manage-contact.php', 'myplugin_admin_page', 'dashicons-tickets', 6  );
}

function cf_shortcode() {
    ob_start();
    form_contact_update_db_check();
    html_form_code();
    save_testimonial();
    my_admin_menu();

	return ob_get_clean();
}

add_shortcode( 'sitepoint_contact_form', 'cf_shortcode' );
?>