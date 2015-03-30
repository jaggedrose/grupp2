<?php
  //find WordPress in our filesystem
  $site_root = explode("/", $_SERVER["REQUEST_URI"]);
  $site_root = $site_root[1];
  $wp_root = substr(__DIR__, 0, stripos(__DIR__, $site_root) + strlen($site_root) + 1);
  require($wp_root . 'wp-load.php');
?>

<section class="container">
  <div id="secondary" class="widget-area  " role="complementary">
    <!-- 
      Ask wordpress to start printing any content from the
      footer sidebar (defined in functions.php)
    -->
    <?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
  </div>
</section>

	 