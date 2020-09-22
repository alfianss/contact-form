<?php
/*
Plugin Name: Contact Form Plugin
Plugin URI: http://example.com
Description: Simple WordPress Contact Form
Version: 1.0
Author: Alfian SS
Author URI: http://w3guy.com
*/
global $con_db_version;
$con_db_version = '1.0';
add_option( "con_db_version", "1.0" );

class Contact_Form {
		
	public function con_install() {
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
	
	public function con_install_data() {
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

	public function form_contact_update_db_check() {
		global $con_db_version;
		if ( get_site_option( 'con_db_version' ) != $con_db_version ) {
			self::con_install();
		}
	}

	public function delete_testimonial() {
		// global $wpdb;

		if(isset($_GET['delete'])) {
			echo $_GET['id'];
		}

	}

	public function contact_form_menu(){
		echo "<div class='wrap'><h2>Manage Contact Form</h2></div>";
		
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
			echo "<tr><td>".$no."</td>
				<td>".$tm->name."</td>
				<td>".$tm->email."</td>
				<td>".$tm->phone_number."</td>
				<td>".$tm->testimoni."</td>
				<td>
					<form id='cf_delete' method='get'>
						<input type='hidden' name='id' value='".$tm->id."'>
						<input type='submit' name='delete' value='Delete'> 
					</form>
					
				</td></tr>";
			$no++;
		}

		echo "</table>";
	}

	public function cf_admin_menu() {
		add_menu_page( 'Contact Form Menu', 'Contact Form', 'manage_options', 'contact-form', array('Contact_Form','contact_form_menu'), 'dashicons-tickets', 6  );
	}

	public function save_testimonial() {
		// if the submit button is clicked, send the email
		if ( isset( $_POST['cf-submitted'] ) ) {
			global $wpdb;
			// sanitize form values
			if(isset ($_POST["cf-name"])) { $name = sanitize_text_field( $_POST["cf-name"] ); } else { $name = null; }
			if(isset ($_POST["cf-email"])) { $email = sanitize_email( $_POST["cf-email"] ); } else { $email = null; }
			if(isset ($_POST["cf-phone"])) { $phone = sanitize_text_field( $_POST["cf-phone"] ); } else { $phone = null; }
			if(isset ($_POST["cf-testimonial"])) { $testimonial = esc_textarea( $_POST["cf-testimonial"] ); } else { $testimonial = null; }
			
			$data_cf 	 = array( 
				'name' => $name, 
				'email' => $email,
				'phone_number' => $phone,
				'testimoni' => $testimonial 
			);
	
			$table = $wpdb->prefix.'testimonial'; 		
			$insertdb = $wpdb->insert($table, $data_cf);
			
			if ( $insertdb ) {
				echo '<div>';
				echo '<p>Thanks for contacting me, expect a response soon.</p>';
				echo '</div>';
			} else {
				echo 'An unexpected error occurred';
			}
		}
		
	}

	public function html_form_code() {
		include( 'templates/forms.php' );
		self::save_testimonial();
	}

	public function cf_shortcode() {
		ob_start();    
		self::html_form_code();    
		return ob_get_clean();
	}
}

$contact_form = new Contact_Form();
require_once( plugin_dir_path( __FILE__ ) . 'class.contact-form-widget.php' );

register_activation_hook( __FILE__, array('Contact_Form','con_install') );
register_activation_hook( __FILE__, array('Contact_Form','con_install_data') );

add_action( 'plugins_loaded', array('Contact_Form','form_contact_update_db_check') );
add_action( 'admin_menu', array('Contact_Form','cf_admin_menu') );

add_shortcode( 'sitepoint_contact_form', array('Contact_Form','cf_shortcode') );
