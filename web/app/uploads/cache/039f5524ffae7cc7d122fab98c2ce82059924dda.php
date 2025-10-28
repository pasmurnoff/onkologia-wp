<!doctype html>
<html <?php echo get_language_attributes(); ?>>
<?php echo $__env->make('components.head.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<body <?php body_class() ?>>
<?php do_action('get_header') ?>
<?php echo $__env->make('components.header.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<div class="wrapper">
  <?php echo $__env->yieldContent('content'); ?>
</div>


<?php do_action('get_footer') ?>
<?php echo $__env->make('components.footer.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php wp_footer() ?>
</body>
</html>
