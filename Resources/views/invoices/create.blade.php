<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Add Site Information</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-invoices.store')}}" id="generateInvoiceForm">
            @csrf
            <div class="form-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="required">Website</label>
                            <select class="form-control select2" name="outreach_site_id" id="websiteSelection">
                                <option value="0">Select</option>
                                @foreach($sites as $site)
                                <option value="{{$site->id}}">{{$site->site}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12" id="backlinksList">
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-success" id="generateInvoiceButton" disabled> <i class="fa fa-check"></i> Generate</button>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">

    $('body #websiteSelection').change(function(){
        url = '{{route('member.outreach-invoices.site-data', ':id')}}';
        url = url.replace(':id', $(this).val());
        $('#backlinksList').html('');
        $('#generateInvoiceButton').prop('disabled', true);

        $.easyAjax({
            url: url,
            type: "GET",
            success: function(res){
                if (res.cost > 0) {
                    $('#generateInvoiceButton').prop('disabled', false);
                } else {
                    $('#generateInvoiceButton').prop('disabled', true);
                }

                $('#backlinksList').html(res.html);
            }
        })
    })

    $("#generateInvoiceForm .select2").select2();

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: []
        });

    $('#generateInvoiceForm').submit(function(e){
        e.preventDefault();

        $.easyAjax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: "POST",
            success: function(res){
                if (res.status == 'success') {
                    window.LaravelDataTables["invoices-table"].draw();
                    $('#invoiceModal').modal('toggle');
                }
            }
        })
    })
</script>