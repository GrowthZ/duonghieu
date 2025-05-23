
<?php $__env->startSection('settings-page'); ?>
<!--settings-->
<form class="form">
    <!--form text tem-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label"><?php echo e(cleanLang(__('lang.contract_prefix'))); ?></label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="settings_contracts_prefix"
                name="settings_contracts_prefix" value="<?php echo e($settings->settings_contracts_prefix ?? ''); ?>">
        </div>
    </div>

    <!--next id-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label"><?php echo app('translator')->get('lang.next_id_number_contract'); ?>
            (<?php echo app('translator')->get('lang.optional'); ?>)
            <!--info tooltip-->
            <span class="align-middle text-themecontrast" data-toggle="tooltip"
                title="<?php echo app('translator')->get('lang.next_id_number_info'); ?>" data-placement="top"><i
                    class="ti-info-alt"></i></span></label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="next_id" name="next_id" value="<?php echo e($next_id); ?>">
            <input type="hidden" name="next_id_current" value="<?php echo e($next_id); ?>">
        </div>
    </div>

    <?php if(config('system.settings_type') == 'standalone'): ?>
    <!--[standalone] - settings documentation help-->
    <div>
        <a href="https://growcrm.io/documentation" target="_blank" class="btn btn-sm btn-info help-documentation"><i
                class="ti-info-alt"></i>
            <?php echo e(cleanLang(__('lang.help_documentation'))); ?></a>
    </div>
    <?php endif; ?>
    
    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/contracts" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable"><?php echo e(cleanLang(__('lang.save_changes'))); ?></button>
    </div>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.ajaxwrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/pages/settings/sections/contracts/page.blade.php ENDPATH**/ ?>