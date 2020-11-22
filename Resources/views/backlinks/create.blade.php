<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Add New Backlink</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-backlinks.store')}}" id="addBacklinkForm">
            @csrf
            <div class="form-body">
                <div class="row" id="backlinksAdditionTab">
                    <div class="col-xs-7" id="website-block">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <select name="outreach_site_id" class="form-control select2">
                                @foreach($sites as $site)
                                <option value="{{$site->id}}">{{$site->site}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-7" id="input-website-block" style="display: none;">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <input type="text" name="website" class="form-control" placeholder="Enter Website">
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="required">Type</label>
                            <select name="type" class="form-control select2">
                                <option value="post">Post</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-1">
                        <div class="form-group">
                            <label class="control-label">Paid</label>
                            <br>
                            <input type="checkbox" class="js-switch" name="paid" id="paidCheckbox" value="1" checked />
                        </div>
                    </div>

                    <div id="infoTab">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label class="required">Project</label>
                                <select name="project_id[]" class="form-control select2">
                                    @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->project_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <div class="form-group">
                                <label class="control-label required">Backlink</label>
                                <input type="text" name="backlink[]" class="form-control" placeholder="Enter Backlink">
                            </div>
                        </div>

                        <div class="col-xs-3">
                            <div class="form-group">
                                <label class="control-label">URL (for Backlink)</label>
                                <input type="text" name="url[]" class="form-control" placeholder="Enter URL">
                            </div>
                        </div>

                        <div class="col-xs-1">
                            <div class="form-group">
                                <label class="control-label">More</label>
                                <button type="button" id="addMoreBacklink" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-xs-2">
                        <div class="form-group">
                            <label class="control-label">Indexed</label>
                            <br>
                            <input type="checkbox" class="js-switch" name="indexed" value="1" />
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label">Remarks (Optional)</label>
                            <textarea class="form-control summernote" rows="5" name="remarks"></textarea>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-success btn-sm"> <i class="fa fa-check"></i> @lang('app.save')</button>
                <button type="button" id="addMoreBacklink" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add Field</button>
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

    $('body #deleteInfo').click(function(){
        property = $(this).parent().parent().parent();

        property.remove();
    });

    $('body #addMoreBacklink').click(function(){
        num = Math.floor(Math.random() * 1000) + 1;
        property = '<div id="infoTab"> <div class="col-xs-4"> <div class="form-group"> <label class="required">Project</label> <select name="project_id[]" class="form-control select2" id="'+num+'"> @foreach($projects as $project) <option value="{{$project->id}}">{{$project->project_name}}</option> @endforeach </select> </div> </div> <div class="col-xs-4"> <div class="form-group"> <label class="control-label required">Backlink</label> <input type="text" name="backlink[]" class="form-control" placeholder="Enter Backlink"> </div> </div> <div class="col-xs-3"> <div class="form-group"> <label class="control-label">URL (for Backlink)</label> <input type="text" name="url[]" class="form-control" placeholder="Enter URL"> </div> </div> <div class="col-xs-1"> <div class="form-group"> <label class="control-label">Delete</label> <button type="button" id="deleteInfo" class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button> </div> </div> </div><script>$("#'+num+'").select2();$("body #deleteInfo").click(function(){ property = $(this).parent().parent().parent(); property.remove(); });';

        $('#backlinksAdditionTab').append(property);
    })

    //Checkbox Style
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html);
    });

    $("#addBacklinkForm .select2").select2();

    $('#paidCheckbox').change(function(){
        if ($(this).prop('checked')) {
            $('#website-block').show();
            $('#input-website-block').hide();
        } else {
            $('#website-block').hide();
            $('#input-website-block').show();
        }
    })

    jQuery('#published_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

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

    $('#addBacklinkForm').submit(function(e){
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