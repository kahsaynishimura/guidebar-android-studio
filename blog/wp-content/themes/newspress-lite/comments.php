<?php
/* 	News Press's Comments Area for Single Pages
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/

	if ( post_password_required() ) { return; }
?>

<div id="commentsbox">
<div id="comments">
<?php if ( have_comments() ) : ?>
	<h2 class="comments"><?php comments_number('No Comments' . '', 'One Comment', '% ' . 'Comments' . '' );  echo ' ' . ' to'; ?> <a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
	<ol class="commentlist">
		<?php wp_list_comments( array( 'avatar_size' => '200' )  ); ?>
	</ol>
	<div class="comment-nav">
		<div class="floatleft">
			<?php previous_comments_link() ?>
		</div>
		<div class="floatright">
			<?php next_comments_link() ?>
		</div>
	</div>
<?php else : ?>
	<?php if ( ! comments_open() && ! is_page() ) : ?>
		<p class="watermark"><?php echo 'Comments are Closed'; ?></p>
	<?php endif; ?>
<?php endif; ?>
<?php if ( comments_open() ) : ?>
	
	<div id="comment-form">
		<?php 
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		
		$newspress_comment_args = array(
  		'id_form'           => 'commentform',
  		'id_submit'         => 'submit',
  		'title_reply'       => 'Leave a Reply',
  		'title_reply_to'    => 'Leave a Reply to' . ' %s',
  		'cancel_reply_link' => 'Cancel Reply',
  		'label_submit'      => 'Post Comment',

  'comment_field' =>  '<p class="comment-form-comment"><label for="comment">' . 'Comment:' .'</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"> </textarea></p>',

  'must_log_in' => '<p class="must-log-in">' . sprintf( 'For Posting a Comment You must be' .' <a href="%s"> ' . 'Logged In' .'</a>.', wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '</p>',

  'logged_in_as' => '<p class="logged-in-as">' . sprintf( 'Logged In as ' .' <a href="%1$s">%2$s</a>, <a href="%3$s" title="' . 'Log out of this account' .'">' . 'Log out?' .'</a>', admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',

  'comment_notes_before' => '<p class="comment-notes">' .  'Your email address will not be published. Required fields are marked as' . '  <span class="required">*</span></p>',

  'comment_notes_after' => '',

  'fields' => apply_filters( 'comment_form_default_fields', array(

    'author' => '<p class="comment-form-author">' . '<label for="author">' . 'Name:' .' ', '' . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>', 

    'email' => '<p class="comment-form-email"><label for="email">' . 'E-Mail:' .' ', '' . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',

    'url' => '<p class="comment-form-url"><label for="url">' . 'Website:' .' ', '' . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'
	
    )
  ),
);
		
	
		
		comment_form($newspress_comment_args); ?>
	</div>
<?php endif; ?>
</div>
</div>
