	<section class="container">
	  <h2>Adress: Adminstreet72<br>Phone number: 666-66666<br>Email: admin@admin.com</h2>

	</section>

	<?php wp_footer(); ?>
		
		
	 <?php if ( is_active_sidebar( 'sidebar-1' )  ) : ?>

	 <div class="widget-area" role="complementary">
	 
	  <?php dynamic_sidebar( 'sidebar-1' ); ?>
	 
	 </div>