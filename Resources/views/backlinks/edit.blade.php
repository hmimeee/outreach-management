<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Edit Backlink Information</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-backlinks.update', $backlink->id)}}" id="updateBacklinkForm">
            @csrf
            @method('patch')
            <div class="form-body">
                <div class="row">
                    <div class="col-xs-6 ">
                        <div class="form-group">
                            <label class="required">Project</label>
                            <select name="project_id" class="form-control select2" {{$backlink->status == 'approved' ? 'disabled' : ''}}>
                                @foreach($projects as $project)
                                <option value="{{$project->id}}" {{$backlink->project_id == $project->id ? 'selected' : ''}}>{{$project->project_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6" id="website-block" style="{{$backlink->paid ? "" : "display: none;"}}">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <select name="outreach_site_id" class="form-control select2" {{$backlink->status == 'approved' ? 'disabled' : ''}}>
                                @foreach($sites as $site)
                                <option value="{{$site->id}}" {{$backlink->outreach_site_id == $site->id ? 'selected' : ''}}>{{$site->site}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-6" id="input-website-block" style="{{$backlink->paid ? "display: none;" : ""}}">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <input type="text" name="website" class="form-control" placeholder="Enter Website" value="{{$backlink->website}}" {{$backlink->status == 'approved' ? 'disabled' : ''}}>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label required">Backlink</label>
                            <input type="text" name="backlink" class="form-control" placeholder="Enter Backlink" value="{{$backlink->backlink}}" @if($backlink->status == 'approved' && (!in_array(auth()->id(), $setting->admins) || !in_array(auth()->id(), $setting->observers))) disabled @endif>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">URL (for Backlink)</label>
                            <input type="text" name="url" class="form-control" placeholder="Enter URL" value="{{$backlink->url}}" {{$backlink->status == 'approved' ? 'disabled' : ''}}>
                        </div>
                    </div>
                    <div class="col-xs-4" id="website-block">
                        <div class="form-group">
                            <label class="required">Type</label>
                            <select name="type" class="form-control select2" {{$backlink->status == 'approved' ? 'disabled' : ''}}>
                                <option value="post" {{$backlink->type =='post' ? 'selected' : ''}}>Post</option>
                                <option value="link" {{$backlink->type =='link' ? 'selected' : ''}}>Link</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-2">
                        <div class="form-group">
                            <label class="control-label">Indexed</label>
                            <br>
                            <input type="checkbox" class="js-switch" name="indexed" value="1" {{$backlink->indexed ? 'checked' : ''}} />
                        </div>
                    </div>

                    <div class="col-xs-2">
                        <div class="form-group">
                            <label class="control-label">Paid</label>
                            <br>
                            <input type="checkbox" class="js-switch" name="paid" id="paidCheckbox" value="1" {{$backlink->paid ? 'checked' : ''}} {{$backlink->status == 'approved' ? 'disabled' : ''}}/>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Remarks (Optional)</label>
                        <textarea class="form-control" rows="5" name="remarks"></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <input type="hidden" name="id" value="{{$backlink->id}}">
                <button class="btn btn-success btn-sm"> <i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>

<script type="text/javascript">
    //Checkbox Style
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html);
    });

    $("#updateBacklinkForm .select2").select2();

    $('#paidCheckbox').change(function(){
        if ($(this).prop('checked')) {
            $('#website-block').show();
            $('#input-website-block').hide();
        } else {
            $('#website-block').hide();
            $('#input-website-block').show();
        }
    })

    $('#updateBacklinkForm').submit(function(e){
        e.preventDefault();

        $.easyAjax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: "POST",
            success: function(res){
                if (res.status == 'success') {
                    window.LaravelDataTables["links-table"].draw();
                    $('#backlinkModal').modal('toggle');
                }
            }
        })
    })
</script>