<?php
/*
Plugin Name: Contact Form Plugin
Plugin URI: http://example.com
Description: Simple WordPress Contact Form
Version: 1.0
Author: Alfian SS
Author URI: http://w3guy.com
*/



class Contact_Form {

	public $con_db_version = '1.0';

	public function con_db_version() {
		return $this->con_db_version;		
	}
		
	public function con_install() {
		global $wpdb;
	
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
		update_option( 'con_db_version', $this->con_db_version() );
	}

	public function form_contact_update_db_check() {
		if ( get_option( 'con_db_version' ) != $this->con_db_version() ) {
			$this->con_install();
		}
	}

	public function delete_testimonial() {
		global $wpdb;
		
		if(isset($_GET['delete']) && isset($_GET['id'])) {
			$id = $_GET['id'];

			$table = $wpdb->prefix.'testimonial';			
			$result = $wpdb->delete( $table, array( 'id' => $id ) );

			
		}

	}

	public function contact_form_menu(){
		echo "<div class='wrap'><h2>Manage Contact Form</h2></div>";
		$this->delete_testimonial();
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
					<a href='".admin_url('admin.php?page=contact-form&delete=true&id='.$tm->id)."'>Delete</a>
				</td></tr>";
			$no++;
		}

		echo "</table>";
	}

	public function cf_admin_menu() {
		add_menu_page( 'Contact Form Menu', 'Contact Form', 'manage_options', 'contact-form', array($this,'contact_form_menu'), 'dashicons-tickets', 6  );
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
		$this->save_testimonial();
	}

	public function cf_shortcode() {
		ob_start();    
		$this->html_form_code();    
		return ob_get_clean();
	}
}

$contact_form = new Contact_Form();
// $contact_form::con_db_version();
require_once( plugin_dir_path( __FILE__ ) . 'class.contact-form-widget.php' );

register_activation_hook( __FILE__, array($contact_form,'con_install') );

add_action( 'plugins_loaded', array($contact_form,'form_contact_update_db_check') );
add_action( 'admin_menu', array($contact_form,'cf_admin_menu') );

add_shortcode( 'sitepoint_contact_form', array($contact_form,'cf_shortcode') );
