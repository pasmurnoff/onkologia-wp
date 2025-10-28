<?php $__env->startSection('content'); ?>
    <div class="container sections-wrapper">

        <?php echo $__env->make('components.search.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('components.category-sections.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('components.latest-topics-section.wrap', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php render_latest_topics_section(); ?>
        <!--  include __DIR__ . ?/partials/search.php?;-->
        <!--  include __DIR__ . ?/partials/categories-section.php?;-->
        <!--  include __DIR__ . ?/partials/latest-topics-section.php?;-->
        <!--  render_latest_topics_section();-->

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>