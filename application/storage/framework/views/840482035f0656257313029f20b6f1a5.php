<div class="row">
    <!--options panel-->
    <?php echo $__env->make('pages.canned.components.panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <div class="col-sm-12 col-lg-9" id="canned-table-container">
        <?php echo $__env->make('pages.canned.components.table.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/pages/canned/components/body.blade.php ENDPATH**/ ?>