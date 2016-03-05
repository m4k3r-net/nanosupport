<?php
/**
 * Email Functions
 *
 * All the responsible functions for emailing for NanoSupport.
 *
 * @author      nanodesigns
 * @category    Email
 * @package     NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Handle Notification Email.
 *
 * @since  1.0.0
 * 
 * @param   string $contact_name    Name of the sender.
 * @param   string $contact_email   Email of the sender.
 * @param   string $contact_subject Subject of the message.
 * @param   string $contact_message Complete message.
 * @return  void
 * ------------------------------------------------------------------------------
 */
function nanosupport_handle_notification_email( $ticket_id = null ) {

    //If the ticket is not submitted, return
    if( !isset( $_POST['ns_submit'] ) )
        return;

    if( null === $ticket_id )
        return;

    /**
     * Generate Dynamic values
     */
    $ticket_edit_link = get_edit_post_link( $ticket_id );
    $ticket_view_link = get_permalink( $ticket_id );

    //Get ticket information
    $ticket_control = get_post_meta( $ticket_id, 'ns_control', true );
    $ticket_priority = $ticket_control['priority'];
    if( 'low' === $ticket_priority )
        $priority = __( 'Low', 'nanosupport' );
    else if( 'medium' === $ticket_priority )
        $priority = __( 'Medium', 'nanosupport' );
    else if( 'high' === $ticket_priority )
        $priority = __( 'High', 'nanosupport' );
    else if( 'critical' === $ticket_priority )
        $priority = __( 'Critical', 'nanosupport' );
    

    //Prepare the Message content
    ob_start(); ?>

    <html lang="en">

        <head>
            <title><?php _e('New Ticket Submitted', 'nanosupport'); ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        </head>

        <body style="line-height:1;font-family:'Book Antiqua',Georgia,'Times New Roman',serif;font-size:15px">
            <div style="width:95%;max-width:600px">
                
                <div id="container" style="border:1px solid #ccc;background-color:#fff;padding:20px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;">

                    <h2 style="font-size:22px;margin-bottom:20px;border-bottom: 3px solid #e1e1e1;padding-bottom:3px;color:#999">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#999;text-decoration:none">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </h2>
                    
                    <h3 style="margin-bottom:10px;font-size:18px">
                        <?php _e('New Ticket Submitted', 'nanosupport'); ?>
                    </h3>

                    <p><?php _e('A customer of you is seeking support. Please find the links below:', 'nanosupport'); ?></p>
                    <div style="margin-bottom:20px;color:#666;margin-left: 20px">
                        <p><a href="<?php echo esc_url($ticket_view_link); ?>" style="color:#2a579c;text-decoration:none;">
                            <?php _e('Link to the Ticket', 'nanosupport'); ?>
                        </a></p>
                        <p><a href="<?php echo esc_url($ticket_edit_link); ?>" style="color:#2a579c;text-decoration:none;">
                            <?php _e('Ticket in edit mode', 'nanosupport'); ?>
                        </a></p>
                    </div>

                    <div style="margin-bottom:10px;color:#666;font-size:14px;background-color:#e1e1e1;padding:5px">
                        <?php printf( __('Priority: <strong>%s</strong>', 'nanosupport'), $priority ); ?>
                    </div>

                    <p style="color:#999;font-size:14px;border-top:3px solid #e1e1e1;padding-top: 3px;font-style:italic"><?php _e('This is an automated email, generated by', 'nanosupport'); ?> <a href="<?php echo esc_url('http://nanosupport.nanodesignsbd.com'); ?>" title="<?php esc_attr_e('NanoSupport - WordPress Ticketing Plugin', 'nanosupport'); ?>" style="color:#2a579c;text-decoration:none;">NanoSupport</a></p>

                </div> <!-- #container -->

            </div>
        </body>
    </html>

    <?php
    $message        = ob_get_clean();

    //wp_mail() Defaults...
    $_sender        = get_bloginfo( 'name' );
    $_from_email    = nanosupport_ondomain_email(); //noreply@yourdomain.dom
    $to_email       = nanosupport_ondomain_email('info'); //info@yourdomain.dom

    $headers        = "From: ". $_sender ." <". $_from_email .">\r\n";
    $headers        .= "Reply-To: ". $_from_email ."\r\n";
    $headers        .= "MIME-Version: 1.0\r\n";
    $headers        .= "Content-Type: text/html; charset=UTF-8";

    function nanosupport_mail_content_type() {
        return "text/html";
    }
    add_filter( 'wp_mail_content_type', 'nanosupport_mail_content_type' );

    //send the email
    $ns_notification_email = wp_mail( $to_email, $subject, $message, $headers );
     
    // If email has been process for sending, display a success message
    if ( ! is_wp_error($ns_notification_email) ) {
        return true;
    } else {
        return $ns_notification_email->get_error_message();
    }
}