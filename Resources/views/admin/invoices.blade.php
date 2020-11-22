@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
	<!-- .page title -->
	<div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
		<h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle }}
			<span class="text-info b-l p-l-10 m-l-5">{{ $totalInvoices ?? 0 }}</span> <span
			class="font-12 text-muted m-l-5"> Total Invoices</span>
		</h4>
	</div>

	<!-- /.page title -->
	<!-- .breadcrumb -->
	<div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
		<a href="javascript:;" class="btn btn-outline btn-success btn-sm" id="generateInvoice">Generate New Invoice <i class="fa fa-plus" aria-hidden="true"></i></a>
		<ol class="breadcrumb">
			<li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
			<li class="active">{{ $pageTitle }}</li>
		</ol>
	</div>
	<!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet"
href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style>
    .btn-pref .btn {
        -webkit-border-radius:0 !important;
    }
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="white-box">

			@section('filter-section')
			<div class="row" id="ticket-filters">

				<form action="" id="filter-form">
					<div class="col-md-12">
						<div class="form-group">
							<h5>Status</h5>
							<select class="select2 form-control" name="status" id="status" data-style="form-control">
								<option value="all">All</option>
								<option value="0">Unpaid</option>
								<option value="1">Paid</option>
							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group p-t-10">
							<label class="control-label col-xs-12">&nbsp;</label>
							<button type="button" id="apply-filters" class="btn btn-success btn-sm col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
							<button type="button" id="reset-filters" class="btn btn-inverse col-md-5 btn-sm col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
						</div>
					</div>
				</form>
			</div>
			@endsection

			@if($errors->any())
			{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
			@endif

			@if (session()->has('success'))
			<div class="alert alert-success">
				{!! session()->get('success')!!}        
			</div>
			@endif

			<div class="table-responsive">
				{!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
			</div>
		</div>
	</div>
</div>
<!-- .row -->
{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="invoiceModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" id="modal-data-application">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
			</div>
			<div class="modal-body">
				Loading...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn default" data-dismiss="modal">Close</button>
				<button type="button" class="btn blue">Save changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalArea" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalArea"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                //
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>

	$('#generateInvoice').click(function(){
		var url = '{{ route('admin.outreach-invoices.create')}}';
		$('#modelHeading').html("Generate New Invoice");
		$.ajaxModal('#invoiceModal', url);
	})

	$(".select2").select2();

	$('#apply-filters').click(function () {
		$('#invoices-table').on('preXhr.dt', function (e, settings, data) {
			var status = $('#status').val();
			data['status'] = status;


		});

		$.easyBlockUI('#invoices-table');
		window.LaravelDataTables["invoices-table"].draw();
		$.easyUnblockUI('#invoices-table');

	});

	$('#reset-filters').click(function () {
		$('#filter-form')[0].reset();
		$('#status').val('all');
		$('.select2').val('all');
		$('#filter-form').find('select').select2();

		$.easyBlockUI('#invoices-table');
		window.LaravelDataTables["invoices-table"].draw();
		$.easyUnblockUI('#invoices-table');
	})

	function viewInvoice(id){
		@if(auth()->user()->hasRole('admin'))
		url = '{{route('admin.outreach-invoices.show', ':id')}}';
		@else
		url = '{{route('member.outreach-invoices.show', ':id')}}';
		@endif
		url = url.replace(':id', id);

		$.ajaxModal('#invoiceModal', url);
	}

	function editInvoice(id){
		@if(auth()->user()->hasRole('admin'))
		url = '{{route('admin.outreach-invoices.edit', ':id')}}';
		@else
		url = '{{route('member.outreach-invoices.edit', ':id')}}';
		@endif
		url = url.replace(':id', id);

		$.ajaxModal('#invoiceModal', url);
	}

	$(function () {
		$('body').on('click', '#deleteInvoice', function () {
			var id = $(this).data('id');

			var buttons = {
                cancel: "Cancel",
                confirm: {
                    text: "Yes",
                    value: 'confirm',
                    visible: true,
                    className: "danger",
                }
            };

            swal({
                title: "Are you sure?",
                text: "Please enter your password below:",
                dangerMode: true,
                icon: 'warning',
                buttons: buttons,
            }).then(function (isConfirm) {
				if (isConfirm) {

					@if(auth()->user()->hasRole('admin'))
					url = '{{route('admin.outreach-invoices.destroy', ':id')}}';
					@else
					url = '{{route('member.outreach-invoices.destroy', ':id')}}';
					@endif
					url = url.replace(':id', id);

					var token = "{{ csrf_token() }}";

					$.easyAjax({
						type: 'POST',
						url: url,
						data: {'_token': token, '_method': 'DELETE'},
						success: function (response) {
							if (response.status == "success") {
								window.LaravelDataTables["invoices-table"].draw();
							}
						}
					});
				}
			});
		});

	});

	$( document ).ready(function(){
		var id = '{{request('view-invoice')}}';
		if (id) {
			viewInvoice(id);
		}
	})
</script>
@endpush
