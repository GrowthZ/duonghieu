<!DOCTYPE html>
<html lang="en" class="logged-out <?php echo e(config('visibility.page_rendering')); ?>">

<!--html header-->
<?php echo $__env->make('layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!--html header-->

<body class="<?php echo e($page['page'] ?? ''); ?>">
    <!--preloader-->
    <?php if(config('visibility.page_rendering') == '' || config('visibility.page_rendering') != 'print-page'): ?>
    <div class="preloader">
        <div class="loader">
            <div class="loader-loading"></div>
        </div>
    </div>
    <?php endif; ?>
    <!--preloader-->

    <!--main content-->
    <div id="main-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!--common modals-->
    <?php echo $__env->make('modals.actions-modal-wrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('modals.common-modal-wrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

<?php echo $__env->make('layout.footerjs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!--js automations-->
<?php echo $__env->make('layout.automationjs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!--[note: no sanitizing required] for this trusted content, which is added by the admin-->
<?php echo config('system.settings_theme_body'); ?>


<!--[PRINTING]-->
<?php if(config('visibility.page_rendering') == 'print-page'): ?>
<script src="public/js/dynamic/print.js?v=<?php echo e(config('system.versioning')); ?>"></script>
<?php endif; ?>

</html><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/layout/wrapperplain.blade.php ENDPATH**/ ?>