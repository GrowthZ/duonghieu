
<?php $__env->startSection('settings-page'); ?>
<!-- action buttons -->
<?php echo $__env->make('pages.settings.sections.milestones.misc.list-page-actions', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- action buttons -->

<!--heading-->
<?php echo $__env->make('pages.settings.sections.milestones.table.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!--section js resource-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.ajaxwrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/pages/settings/sections/milestones/table/page.blade.php ENDPATH**/ ?>