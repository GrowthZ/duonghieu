
<?php $__env->startSection('settings-page'); ?>
<!--settings-->
<form class="form" id="settingsFormFolders">
    <!--item-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label"><?php echo app('translator')->get('lang.file_folders'); ?></label>
        <div class="col-sm-12">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="settings2_file_folders_status" name="settings2_file_folders_status"
                data-preselected="<?php echo e($settings->settings2_file_folders_status ?? ''); ?>">
                <option value="enabled"><?php echo app('translator')->get('lang.enabled'); ?></option>
                <option value="disabled"><?php echo app('translator')->get('lang.disabled'); ?></option>
            </select>
        </div>
    </div>

    <div class="line"></div>


    <div class="card-contrast-panel <?php echo e(runtimeVisibility('settings-file-folders-manage', $settings->settings2_file_folders_status)); ?>"
        id="file_folders_managers">

        <h6 class="text-underlined m-b-16"><?php echo app('translator')->get('lang.users_that_can_manage_folders'); ?></h6>

        <!--settings2_file_folders_manage_assigned-->
        <div class="form-group form-group-checkbox row">
            <label class="col-sm-12 col-lg-4 col-form-label text-left"><?php echo app('translator')->get('lang.assigned_users'); ?></label>
            <div class="col-sm-12 col-lg-8 text-left p-t-5">
                <input type="checkbox" id="settings2_file_folders_manage_assigned"
                    name="settings2_file_folders_manage_assigned" class="filled-in chk-col-light-blue"
                    <?php echo e(runtimePrechecked($settings->settings2_file_folders_manage_assigned ?? '')); ?>>
                <label class="p-l-30" for="settings2_file_folders_manage_assigned"></label>
            </div>
        </div>


        <!--settings2_file_folders_manage_project_manager-->
        <div class="form-group form-group-checkbox row">
            <label class="col-sm-12 col-lg-4 col-form-label text-left"><?php echo app('translator')->get('lang.project_manager'); ?></label>
            <div class="col-sm-12 col-lg-8 text-left p-t-5">
                <input type="checkbox" id="settings2_file_folders_manage_project_manager"
                    name="settings2_file_folders_manage_project_manager" class="filled-in chk-col-light-blue"
                    <?php echo e(runtimePrechecked($settings->settings2_file_folders_manage_project_manager ?? '')); ?>>
                <label class="p-l-30" for="settings2_file_folders_manage_project_manager"></label>
            </div>
        </div>

        <!--settings2_file_folders_manage_client-->
        <div class="form-group form-group-checkbox row">
            <label class="col-sm-12 col-lg-4 col-form-label text-left"><?php echo app('translator')->get('lang.client'); ?></label>
            <div class="col-sm-12 col-lg-8 text-left p-t-5">
                <input type="checkbox" id="settings2_file_folders_manage_client"
                    name="settings2_file_folders_manage_client" class="filled-in chk-col-light-blue"
                    <?php echo e(runtimePrechecked($settings->settings2_file_folders_manage_client ?? '')); ?>>
                <label class="p-l-30" for="settings2_file_folders_manage_client"></label>
            </div>
        </div>
    </div>

    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request" data-url="/settings/files/folders"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable"><?php echo e(cleanLang(__('lang.save_changes'))); ?></button>
    </div>

</form>

<?php if(config('system.settings_type') == 'standalone'): ?>
<!--[standalone] - settings documentation help-->
<a href="https://growcrm.io/documentation" target="_blank" class="btn btn-sm btn-info help-documentation"><i
        class="ti-info-alt"></i>
    <?php echo e(cleanLang(__('lang.help_documentation'))); ?>

</a>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.ajaxwrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/pages/settings/sections/files/folders.blade.php ENDPATH**/ ?>