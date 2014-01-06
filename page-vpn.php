<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content vpn-page" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail(); ?>
						</div>
						<?php endif; ?>

						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p>We have now put in place a system of individual user accounts to access the DU Pirate Party VPN. Please register below.</p>
						<?php 

						//Connect to RADIUS SQL Database
						global $wpdb;						

						//Begin to validate form data
						if($_POST['action']=="register"){

							//Begin to validate registration data
							$username = $_POST['username'];
							$password = $_POST['password'];
							$passwordConfirm = $_POST['passwordConfirm'];
							$email = $_POST['email'];

							if(!isset($username)){
								$usernameError = "<span class='error'>Please enter a username</span>";
								$error = 1;
							} else if(!preg_match('/^[\w\-]+$/', $username)) {
								$usernameError = "<span class='error'>Username can be alphanumeric only</span>";
								$error = 1;
							} else if(strlen($username)<4){
								$usernameError = "<span class='error'>Username must be at least 4 characters</span>";
								$error = 1;
							} else { 
								$username_found = $wpdb->query($wpdb->prepare("SELECT * FROM `radcheck` WHERE `username` = %s", $username));
								if($username_found!=0){
									$usernameError = "<span class='error'>This username already exists</span>";
									$error = 1;
								}
							}

							if(strlen($password)<8){
								$passwordError = "<span class='error'>Password must be at least 8 characters long</span>";
								$error = 1;
							} if($password!=$passwordConfirm){
								$passwordConfirmError = "<span class='error'>Your passwords did not match, please try again.</span>";
								$password = ''; $passwordConfirm = '';
								$error = 1;
							}

						  	//Validate email is valid, and that user is member and not already registered.
					     	$email = filter_var($email, FILTER_SANITIZE_EMAIL);  
							if(!isset($username)){
								$emailError = "<span class='error'>Please enter the email you signed up to the Pirate Party with.</span>";
								$error = 1;
							} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
					        	$emailError = "<span class='error'>Not a valid email</span>";
								$error = 1;
					     	} else {
					     		$email_found = $wpdb->query($wpdb->prepare("SELECT * FROM `pirates_members` WHERE `email` = %s", $email));
								if($email_found==0){ //Check if email on members list
									$emailError = "<span class='error'>Email not found on members list. Please use the email you joined the Pirate Party with or join by coming to a weekly meeting or event.</span>";
									$error = 1;
								} else { //Ensure they don't already have an account
									$user_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM `pirates_members` WHERE `email` = %s", $email));
									$other_username = $user_info->vpnUsername;
									if(!empty($other_username)){
										$generalError = "<span class='error'>This email address has an account with the username '$other_username'. Unfortunatly we don't have functionality to reset your password yet. Please contact one of the Pirate Party administrators at info@pirates.ie.</span>";
										$error = 1;
									}
								}
						  	}
							

							if($error!=1){
								//Create user

								//Create a salted SHA1 hash for the user
								mt_srand((double)microtime()*1000000);
								$salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
								$hash = base64_encode(pack("H*", sha1($password . $salt)) . $salt);

								$secureHash = wp_hash_password($password); //This will be in wordpress/PHPASS format.

								if (!$wpdb->insert('radcheck',array( 'username' => $username, 'attribute' => 'SSHA-Password', 'op' => ':=', 'value' => $hash), array('%s', '%s', '%s', '%s'))) { die('MySQL error when inserting user into "radcheck" table.'); }
								if (!$wpdb->update('pirates_members',array( 'vpnUsername' => $username,	'passwordHash' => $secureHash),array( 'email' => $email), array('%s', '%s'), array( '%s'))) { die('MySQL error when updating "pirates_members" table.'); }

								$subject = 'Pirates.ie VPN Account Created';

								$message = 'Hi,'."\n";
								$message .= "You pirates.ie VPN account has been created successfully. You should now be able to log in to the VPN with your username, '$username', and the password you provided when signing up.\n\n";
								$message .= "You can download the OpenVPN config files at https://pirates.ie/vpn.zip. If you have any issues, we have a VPN guide available at https://pirates.ie/vpn/ or you can join us on IRC."."\n\n";
								$message .= "DU Pirate Party Committee.\n";

								$headers = 'From: pirate party <noreply@pirates.ie>' . "\r\n" .
									 'Reply-To: pirate@csc.tcd.ie' . "\r\n" .
									 'X-Mailer: PHP/' . phpversion();

								if(!wp_mail($email, $subject, $message, $headers)){
									echo "<!-- Could not send email -->";
								}			
								$success = 1;
							} 
						} //End register block
					?>
					<?php if(isset($errorMsg)){ echo $errorMsg; } ?>
					<?php if($success!=1) { ?>
						<form method="POST" id="registerform">
							<?php if(isset($generalError)){ echo $generalError; } ?>
							<div class="clearfix"></div>
							<div class="formrow">
								<label for="email">Email</label>
						   		<input type='text' name='email' value="<?php echo $email; ?>" />
								<?php if(isset($emailError)){ echo $emailError; } ?>
								<div class="clearfix"></div>
							</div>
							<div class="formrow">
								<label for="username">Username</label>
								<input type='text' name='username' value="<?php echo $username; ?>" />
								<?php if(isset($usernameError)){ echo $usernameError; } ?>
								<div class="clearfix"></div>
							</div>
							<div class="formrow">
								<label for="password">Password</label>
								<input type='password' name='password' value="<?php echo $password; ?>" />
								<?php if(isset($passwordError)){ echo $passwordError; } ?>
								<div class="clearfix"></div>
							</div>
							<div class="formrow">
								<label for="passwordConfirm">Password (confirm)</label>
								<input type='password' name='passwordConfirm' value="<?php echo $passwordConfirm; ?>" />
								<?php if(isset($passwordConfirmError)){ echo $passwordConfirmError; } ?>
								<div class="clearfix"></div>
							</div>
							<input type="hidden" name="action" value="register" />

							<div class="formrow">
								<input type="submit" value="Register" />
								<div class="clearfix"></div>
							</div>
						</form>
						<?php the_content(); ?>
						<?php } else { ?>
							<h3>Successfully Registered</h3>
							<p>Your account has been registered successfully. You will now be able to connect to the VPN. </p>
							<p>Please download the <a href="/vpn.zip">VPN config files</a>.
							If you have any issues, please refer to our guide for getting set up on Windows 
							<a href="https://pirates.ie/wp-content/uploads/2013/11/VPN_guide_for_windows_7_and_8.pdf">here</a>.
							Here is the guide for getting set up on <a href="https://pirates.ie/wp-content/uploads/2013/11/VPNguidelinuxubuntu.pdf">ubuntu</a>. 
							We also have a guide for getting set up on Mac OS <a href="https://pirates.ie/wp-content/uploads/2013/10/VPNguideforMac.pdf">here</a>. 
							You can join us on IRC. On DUCSS/Netsoc IRC we are at #pirateparty.</p>
						<?php } ?>	
						<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>