@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
	<!-- .page title -->
	<div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
		<h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle }}</h4>
	</div>

	<!-- /.page title -->
	<!-- .breadcrumb -->
	<div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
		<ol class="breadcrumb">
			<li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
			<li class="active">{{ $pageTitle }}</li>
		</ol>
	</div>
	<!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="white-box">
			@if($errors->any())
			{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
			@endif

			@if (session()->has('success'))
			<div class="alert alert-success">
				{!! session()->get('success')!!}        
			</div>
			@endif

			<div class="row p-20">
				<div class="col-md-12">
					<form id="settingsUpdate" method="post">
						@csrf
						<div class="form-group">
							<label class="required">Admins</label>
							<select name="admins[]" class="select2 select2-multiple " multiple="multiple">
								@foreach($users as $u)
								<option value="{{$u->id}}" {{isset($setting->admins) ? (in_array($u->id, $setting->admins) ? 'selected' : '') : ''}}>{{$u->name}}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label class="required">Maintainers</label>
							<select name="maintainers[]" class="select2 select2-multiple " multiple="multiple">
								@foreach($users as $u)
								<option value="{{$u->id}}" {{isset($setting->maintainers) ? (in_array($u->id, $setting->maintainers) ? 'selected' : '') : ''}}>{{$u->name}}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label class="required">Observers</label>
							<select name="observers[]" class="select2 select2-multiple " multiple="multiple">
								@foreach($users as $u)
								<option value="{{$u->id}}" {{isset($setting->observers) ? (in_array($u->id, $setting->observers) ? 'selected' : '') : ''}}>{{$u->name}}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label class="required">Financers</label>
							<select name="financers[]" class="select2 select2-multiple " multiple="multiple">
								@foreach($users as $u)
								<option value="{{$u->id}}" {{isset($setting->financers) ? (in_array($u->id, $setting->financers) ? 'selected' : '') : ''}}>{{$u->name}}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<button class="btn btn-success btn-sm">Update</button>
						</div>
					</form>
				</div>

				<div class="col-md-6">
					<form id="updateModule">
						@csrf
						<div class="form-group btn-group">
							<label>Update Module</label>
							<input type="file" class="form-control" name="package" accept=".zip">
						</div>

						<div class="form-group">
							<button class="btn btn-sm btn-success">Upload</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- .row -->
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script type="text/javascript">
	$(".select2").select2({
		formatNoMatches: function () {
			return "{{ __('messages.noRecordFound') }}";
		}
	});

	$('#settingsUpdate').submit(function(e){
		e.preventDefault();

		url = '{{route('admin.outreach-settings.update')}}';

		$.easyAjax({
			url: url,
            data: $(this).serialize(),
			type: "POST",
			success: function(res){
				if (res.status == 'success') {
					location.reload();
				}
			}
		})
	})

	$('#updateModule').submit(function(e){
		e.preventDefault();

		url = '{{route('admin.outreach-settings.update-module')}}';

		$.easyAjax({
			url: url,
			container: '#updateModule',
			type: "POST",
			file: true,
			success: function(res){
				if (res.status == 'success') {
					location.reload();
				}
			}
		})
	})
</script>
@endpush