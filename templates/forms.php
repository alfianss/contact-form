<?php

echo '<form action="#cf_form" id="cf_form" method="post">'.
     '<p>'.
     'Your Name (required) <br/>'.
     '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />'.
     '</p>'.
     '<p>'.
     'Your Email (required) <br/>'.
     '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />'.
     '</p>'.
     '<p>'.
     'Phone Number <br/>'.
     '<input type="text" name="cf-phone" value="' . ( isset( $_POST["cf-phone"] ) ? esc_attr( $_POST["cf-phone"] ) : '' ) . '" size="40" />'.
     '</p>'.
     '<p>'.
     'Testimonial<br/>'.
     '<textarea rows="10" cols="35" name="cf-testimonial">' . ( isset( $_POST["cf-testimonial"] ) ? esc_attr( $_POST["cf-testimonial"] ) : '' ) . '</textarea>'.
     '</p>'.
     '<p><input type="submit" name="cf-submitted" value="Send"></p>'.
     '</form>';