@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.timesheets.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    @if(auth()->user()->is_team)
    <div class="stats-wrapper" id="invoices-stats-wrapper">
        @include('misc.list-pages-stats-3')
    </div>
    @endif
    <!--stats panel-->

    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <!--timesheets table-->
            @include('pages.timesheets.components.table.wrapper')
            <!--timesheets table-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
<!--task modal-->
@include('pages.task.modal')
@endsection