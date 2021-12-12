@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
	<!-- .page title -->
	<div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
		<h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle }}
			<span class="text-info b-l p-l-10 m-l-5">{{ $totalBacklinks ?? 0 }}</span> <span
			class="font-12 text-muted m-l-5"> Total Backlinks</span>
		</h4>
	</div>

	<!-- /.page title -->
	<!-- .breadcrumb -->
	<div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
		<a href="javascript:;" class="btn btn-outline btn-success btn-sm" id="addCandidate">Add New Backlink <i class="fa fa-plus" aria-hidden="true"></i></a>
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
								<option value="pending">Pending</option>
								<option value="approved">Approved</option>
								<option value="rejected">Rejected</option>
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
				{!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable', 'id' => 'links-table']) !!}
			</div>
		</div>
	</div>
</div>
<!-- .row -->
{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="backlinkModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
@endsection

@push('footer-script')
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

	$('#addCandidate').click(function(){
		var url = '{{ route('admin.outreach-backlinks.create')}}';
		$('#modelHeading').html("Add New Candidate");
		$.ajaxModal('#backlinkModal', url);
	})

	$(".select2").select2();

	$('#apply-filters').click(function () {
		$('#links-table').on('preXhr.dt', function (e, settings, data) {
			var status = $('#status').val();
			data['status'] = status;


		});

		$.easyBlockUI('#links-table');
		window.LaravelDataTables["links-table"].draw();
		$.easyUnblockUI('#links-table');

	});

	$('#reset-filters').click(function () {
		$('#filter-form')[0].reset();
		$('#status').val('all');
		$('.select2').val('all');
		$('#filter-form').find('select').select2();

		$.easyBlockUI('#links-table');
		window.LaravelDataTables["links-table"].draw();
		$.easyUnblockUI('#links-table');
	})

	function viewLink(id){
		@if(auth()->user()->hasRole('admin'))
		url = '{{route('admin.outreach-backlinks.show', ':id')}}';
		@else
		url = '{{route('member.outreach-backlinks.show', ':id')}}';
		@endif
		url = url.replace(':id', id);

		$.ajaxModal('#backlinkModal', url);
	}

	function editLink(id){
		@if(auth()->user()->hasRole('admin'))
		url = '{{route('admin.outreach-backlinks.edit', ':id')}}';
		@else
		url = '{{route('member.outreach-backlinks.edit', ':id')}}';
		@endif
		url = url.replace(':id', id);

		$.ajaxModal('#backlinkModal', url);
	}

	$(function () {
		$('body').on('click', '#deleteBacklink', function () {
			var id = $(this).data('id');

			swal({
				title: "Are you sure?",
				text: "You will not be able to recover the deleted backlink!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel please!",
				closeOnConfirm: true,
				closeOnCancel: true
			}, function (isConfirm) {
				if (isConfirm) {

					var url = "{{ route('admin.outreach-backlinks.destroy',':id') }}";
					url = url.replace(':id', id);

					var token = "{{ csrf_token() }}";

					$.easyAjax({
						type: 'POST',
						url: url,
						data: {'_token': token, '_method': 'DELETE'},
						success: function (response) {
							if (response.status == "success") {
								window.LaravelDataTables["links-table"].draw();
							}
						}
					});
				}
			});
		});

	});

	$( document ).ready(function(){
		var id = '{{request('view-link')}}';
		if (id) {
			viewLink(id);
		}
	})
</script>
@endpush
