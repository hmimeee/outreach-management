<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Edit Site Information</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-management.update', $site->id)}}" id="updateSiteForm">
            @csrf
            @method('patch')
            <div class="form-body">
                <div class="row">
                    <div class="col-xs-6 ">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <input type="text" name="website" class="form-control" value="{{ $site->website }}" disabled>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label required">Niche</label>
                            <input type="text" name="niche" class="form-control" value="{{ $site->niche }}">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Domain Rating</label>
                            <input type="text" name="domain_rating" class="form-control" placeholder="Enter Domain Rate" value="{{ $site->domain_rating }}">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Traffic (Monthly)</label>
                            <input type="text" name="traffic" id="interview_date" class="form-control" placeholder="Enter Monthly Traffic" autocomplete="off" value="{{ $site->traffic }}">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Traffic Value</label>
                            <input type="text" name="traffic_value" class="form-control" placeholder="Enter Traffic Value" value="{{ $site->traffic_value }}">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Post Price</label>
                            <input type="number" name="post_price" class="form-control" placeholder="Enter Post Price" value="{{ $site->post_price }}" step="any" {{in_array(auth()->id(), $setting->maintainers) && $site->status =='approved' ? 'disabled' : ''}}>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Link Price</label>
                            <input type="number" name="link_price" class="form-control" placeholder="Enter Link Price" value="{{ $site->link_price }}" step="any" {{in_array(auth()->id(), $setting->maintainers) && $site->status =='approved' ? 'disabled' : ''}}>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Spam Score</label>
                            <input type="text" name="spam_score" class="form-control" placeholder="Enter Spam Score" value="{{ $site->spam_score }}">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Ahref Link</label>
                            <input type="text" name="ahref_link" class="form-control" placeholder="Enter Ahref Link" value="{{ $site->ahref_link }}">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Ahref Snap Link (Screenshot)</label>
                            <input type="text" name="ahref_snap" class="form-control" placeholder="Enter Ahref Snap Link" value="{{ $site->ahref_snap }}">
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Notes (Optional)</label>
                        <textarea class="form-control summernote" rows="5" name="notes">{{ $site->notes }}</textarea>
                    </div>
                </div>
            </div>

        <div class="card-footer">
            <button class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
    </form>
    </div>
</div>


<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">

    $("#updateSiteForm .select2").select2();

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
            ]
        });

    $('#updateSiteForm').submit(function(e){
        e.preventDefault();

        $.easyAjax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: "POST",
            success: function(res){
                if (res.status == 'success') {
                    window.LaravelDataTables["sites-table"].draw();
                    $('#siteModal').modal('toggle');
                }
            }
        })
    })
</script>