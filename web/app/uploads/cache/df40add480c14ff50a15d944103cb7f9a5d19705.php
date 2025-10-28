<?php
    // Параметры запроса: последние посты, 9 на страницу
    $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

    $events = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => 9,
        'paged' => $paged,
        'ignore_sticky_posts' => true,
    ]);
?>

<?php $__env->startSection('content'); ?>
    <section class="events container_rg">
        <h1 class="page-title"><?php echo e(get_the_title()); ?> helllow</h1>

    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>