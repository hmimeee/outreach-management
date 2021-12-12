<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Add Site Information</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-management.store')}}" id="addSiteForm">
            @csrf
            <div class="form-body">
                <div class="row">
                    <div class="col-xs-6 ">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <input type="text" name="website" class="form-control" placeholder="Enter Website">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label required">Niche</label>
                            <input type="text" name="niche" class="form-control" placeholder="Enter Website Niche">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Domain Rating</label>
                            <input type="text" name="domain_rating" class="form-control" placeholder="Enter Domain Rate">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Traffic (Monthly)</label>
                            <input type="text" name="traffic" class="form-control" placeholder="Enter Monthly Traffic">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Traffic Value</label>
                            <input type="text" name="traffic_value" class="form-control" placeholder="Enter Traffic Value">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Post Price</label>
                            <input type="number" name="post_price" class="form-control" placeholder="Enter Post Price" step="any">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Link Price</label>
                            <input type="number" name="link_price" class="form-control" placeholder="Enter Link Price" step="any">
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="control-label">Spam Score</label>
                            <input type="number" name="spam_score" class="form-control" placeholder="Enter Spam Score">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label">Ahref Link</label>
                            <input type="text" name="ahref_link" class="form-control" placeholder="Enter Ahref Link">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label">Ahref Snap Link (Screenshot)</label>
                            <input type="text" name="ahref_snap" class="form-control" placeholder="Enter Ahref Snap Link">
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Notes (Optional)</label>
                        <textarea class="form-control summernote" rows="5" name="notes"></textarea>
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

    $("#addSiteForm .select2").select2();

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

    $('#addSiteForm').submit(function(e){
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