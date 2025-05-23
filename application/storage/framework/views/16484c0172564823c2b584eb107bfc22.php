<div class="setup-inner-steps setup-requirements">

    <h5 class="text-info"> Admin User Details </h5>
    <form class="form-horizontal form-material" id="setupForm" name="setupForm">
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">First Name</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="first_name" name="first_name">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Last Name</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="last_name" name="last_name">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Email Address</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="text" value="" id="email" name="email">
            </div>
        </div>
        <div class="form-group m-t-40 row">
            <label for="example-text-input" class="col-4 col-form-label">Password</label>
            <div class="col-8">
                <input class="form-control form-control-sm" type="password" value="" id="password" name="password">
            </div>
        </div>


        <div class="form-group form-group-checkbox row p-t-30">
            <div class="col-12 p-t-5">
                <input type="checkbox" id="optin" name="optin" class="filled-in chk-col-light-blue" checked>
                <label class="p-l-30" for="optin">Keep me informed about Grow CRM updates</label>
            </div>
        </div>

        <!--continue-->
        <!--continue-->
        <div class="x-button text-right p-t-20">
            <button class="btn waves-effect waves-light btn-info btn-extra-padding" data-button-loading-annimation="yes"
                data-button-disable-on-click="yes" data-type="form" data-ajax-type="post" data-form-id="setupForm"
                id="continueButton" type="submit" data-url="<?php echo e(url('setup/adminuser')); ?>">Continue</button>
        </div>
    </form>
</div><?php /**PATH /home/flashvps/duonghieu.3stech.io.vn/application/resources/views/pages/setup/admin.blade.php ENDPATH**/ ?>