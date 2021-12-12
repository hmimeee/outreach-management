<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<table class="table table-bordered table-hover">
    <tr>
        <th>Backlink</th>
        <th>Website</th>
        <th>URL</th>
        <th align="right">Cost</th>
    </tr>
    @foreach($links as $link)
    <tr>
        <td>{{$link->backlink}}</td>
        <td>{{$link->site->site}}</td>
        <td>{{$link->url}}</td>
        <td align="right">{{$link->type == 'post' ? $link->site->post_price : $link->site->link_price}}$</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="3" align="right">Total Cost</td>
        <td align="right">{{number_format($cost, 2)}}$</td>
    </tr>
</table>


<div class="row">
    <div class="col-xs-4">
        <div class="form-group">
            <label class="required">Select Reviewer</label>
            <select class="form-control" name="reviewer_id" id="reviewer">
                @foreach($reviewers as $reviewer)
                <option value="{{$reviewer->id}}">{{$reviewer->name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-xs-2">
        <div class="form-group">
            <label class="required">Payment Method</label>
            <select class="form-control" name="payment_method" id="payment_method">
                <option value="paypal">Paypal</option>
                <option value="payoneer">Payoneer</option>
                <option value="bkash">bKash</option>
                <option value="rocket">Rocket</option>
                <option value="bank transfer">Bank Transfer</option>
                <option value="others">Others</option>
            </select>
        </div>
    </div>

    <div class="col-xs-6">
        <div class="form-group">
            <label>Seller Name</label>
            <input type="text" name="seller" placeholder="Seller Name" class="form-control">
        </div>
    </div>

    <div class="col-xs-12">
        <div class="form-group">
            <label class="required">Payment Details</label>
            <textarea name="payment_details" placeholder="Payment Details" class="form-control summernote"></textarea>
        </div>
    </div>
</div>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script type="text/javascript">
    $("#payment_method").select2();
    $("#reviewer").select2();

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: []
        });
</script>