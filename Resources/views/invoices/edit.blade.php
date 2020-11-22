<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">Edit Invoice Information</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <form method="post" enctype="multipart/form-data" action="{{route('admin.outreach-invoices.update', $invoice->id)}}" id="updateInvoiceForm">
            @csrf
            @method('patch')
            <div class="form-body">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="form-group">
                            <label class="required">Select Reviewer</label>
                            <select class="form-control" name="reviewer_id" id="reviewer">
                                @foreach($reviewers as $reviewer)
                                <option value="{{$reviewer->id}}" {{$invoice->reviewer_id == $reviewer->id ? 'selected' : ''}}>{{$reviewer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-2">
                        <div class="form-group">
                            <label class="required">Payment Method</label>
                            <select class="form-control select2" name="payment_method">
                                <option value="paypal" {{$invoice->payment_method == 'paypal' ? 'selected' : ''}} >Paypal</option>
                                <option value="payoneer" {{$invoice->payment_method == 'payoneer' ? 'selected' : ''}}>Payoneer</option>
                                <option value="bkash" {{$invoice->payment_method == 'bkash' ? 'selected' : ''}}>bKash</option>
                                <option value="rocket" {{$invoice->payment_method == 'rocket' ? 'selected' : ''}}>Rocket</option>
                                <option value="bank transfer" {{$invoice->payment_method == 'bank transfer' ? 'selected' : ''}}>Bank Transfer</option>
                                <option value="others" {{$invoice->payment_method == 'others' ? 'selected' : ''}}>Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Seller Name</label>
                            <input type="text" name="seller" placeholder="Seller Name" class="form-control" value="{{$invoice->seller}}">
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="required">Payment Details</label>
                            <textarea name="payment_details" placeholder="Payment Details" class="form-control summernote">{{$invoice->payment_details}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-success btn-sm"> <i class="fa fa-check"></i> Update</button>
            </div>
        </form>
    </div>
</div>


<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">

    $("#updateInvoiceForm .select2").select2();
    $("#reviewer").select2();

    $('.summernote').summernote({
    height: 100,                 // set editor height
    minHeight: null,             // set minimum height of editor
    maxHeight: null,             // set maximum height of editor
    focus: false,
    toolbar: []
});

    $('#updateInvoiceForm').submit(function(e){
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