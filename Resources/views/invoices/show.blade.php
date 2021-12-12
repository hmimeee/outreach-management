<style>
    .btn-pref .btn {
        -webkit-border-radius:0 !important;
    }

    .swal-footer {
        text-align: center !important;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <i class="ti-id-badge"></i> Invoice {{ $invoice->name }}
        <a href="javascript:;" onclick="copyLink()" class="btn btn-xs btn-default m-l-10"><i class="fa fa-link"></i></a>
        <a target="_blank" href="{{user()->hasRole('admin') ? route('admin.outreach-invoices.print', $invoice->id) : route('member.outreach-invoices.print', $invoice->id) }}" class="btn btn-xs btn-default m-l-10"><i class="fa fa-print"></i></a>
    </h4>
</div>
<div class="modal-body">

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#information">Information</a></li>
        <li><a data-toggle="tab" href="#comments">Comments</a></li>
        <li><a data-toggle="tab" href="#activity">Activity</a></li>
    </ul>

    <div class="tab-content">
        <div id="information" class="tab-pane fade in active">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Website</label>
                        <p>
                            @if($user->hasRole('admin'))
                            <a target="_blank" href="{{route('admin.outreach-management.index')}}?view-site={{$invoice->outreach_site_id}}">{{ $invoice->site->site }} </a>
                            @else
                            <a target="_blank" href="{{route('admin.outreach-management.index')}}?view-site={{$invoice->outreach_site_id}}">{{ $invoice->site->site }} </a>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Amount</label>
                        <p class="text-danger">${{ $invoice->amount }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Reviewer</label>
                        <p>
                            @if($invoice->checkedBy)
                            <img class="img-circle" src="@if($invoice->checkedBy->image !=null) /user-uploads/avatar/{{$invoice->checkedBy->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt=""> <a target="_blank" href="{{route('admin.employees.show',$invoice->checkedBy->id)}}">{{$invoice->checkedBy->name}}</a>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment Status</label>
                        <p>
                            @if($invoice->review)
                            @php($class = [0 => 'danger', 1 => 'success'])

                            {!! '<span class="label label-'.$class[$invoice->status].'">'.($invoice->status ? 'Paid' : 'Unpaid').'</span>' !!}
                            @else
                            <span class="label label-warning">Waiting for Review</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment Date</label>
                        <p class="{{ $invoice->status ? 'text-success' : '' }}">
                            {{ $invoice->status ? (Carbon\Carbon::create($invoice->payment_date)->format('d M Y') ?? '--') : '--' }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Generated</label>
                        <p>
                            {{ $invoice->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Seller</label>
                        <p>{{ $invoice->seller }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <p>
                            {{ ucwords($invoice->payment_method) }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Payment Details</label>
                        <p>
                            {!! $invoice->payment_details !!}
                        </p>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Backlink</th>
                            <th>Backlink Details</th>
                            <th>Type</th>
                            <!-- <th>URL</th> -->
                            <th>Status</th>
                            <th align="right">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($cost = 0)
                        @foreach($invoice->backlinks as $key => $link)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>
                                Backlink: <a target="_blank" href="{{$link->backlink}}">{{\Illuminate\Support\Str::limit($link->backlink, 40)}}</a>
                                <br>
                                URL: <a target="_blank" href="{{$link->url}}">{{$link->url}}</a>
                            </td>
                            <td>
                                <a target="_blank" href="{{$user->hasRole('admin') ? route('admin.outreach-backlinks.index') : route('member.outreach-backlinks.index') }}?view-link={{$link->id}}">View Details</a>
                            </td>
                            <td>{{ucfirst($link->type)}}</td>
                            <!-- <td>
                                <a target="_blank" href="{{$link->url}}">{{$link->url}}</a>
                            </td> -->
                            <!-- <td>
                                <a target="_blank" href="{{$user->hasRole('admin') ? route('admin.projects.show', $link->project_id) : route('member.projects.show', $link->project_id) }}">{{$link->project->project_name}}</a>
                            </td> -->
                            <td>
                                @if(in_array(auth()->id(), $setting->observers))
                                <select class="form-control" style="border: 2px solid @if($link->status =='approved') #00c292 @elseif($link->status =='rejected') #e15931 @else #fec107 @endif" id="link-status" data-id="{{$link->id}}">
                                    <option value="pending" {{$link->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                    <option value="approved" {{$link->status == 'approved' ? 'selected' : ''}}>Approved</option>
                                    <option value="rejected" {{$link->status == 'rejected' ? 'selected' : ''}}>Rejected</option>
                                </select>
                                <div style="display: none;" id="reject-link-tab">
                                 <textarea class="form-control m-t-10" id="link-remarks" placeholder="Type the reason behind" rows="5" maxlength="255"></textarea>
                                 <button class="btn btn-danger btn-sm m-t-10" id="link-reject" data-id="{{$link->id}}">Reject</button>
                                 <button class="btn bg-inverse text-white btn-sm m-t-10" id="link-reject-cancel" data-status="{{$link->status}}">Cancel</button>
                             </div>
                             @else
                             <label class="label @if($link->status =='approved') label-success @elseif($link->status =='rejected') label-danger @else label-warning @endif ">{{ucfirst($link->status)}}</label>
                             @endif
                         </td>
                         <td align="right">${{$link->type == 'post' ? $link->site->post_price : $link->site->link_price}}</td>
                     </tr>
                     @php($cost += ($link->type == 'post' ? $link->site->post_price : $link->site->link_price))
                     @endforeach
                     <tr>
                        <td colspan="5" align="right">Total Cost</td>
                        <td align="right">${{number_format($cost, 2)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row m-t-20">
             @php($lastcom = $invoice->comments->last())
            <div class="col-md-6">
                @if($lastcom)
                <p>
                <img class="img-circle" src="@if($lastcom->user->image !=null) /user-uploads/avatar/{{$lastcom->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="" data-toggle="tooltip" data-placement="top" title="{{$lastcom->user->name}}">
                <b>{{$lastcom->message ?? ''}}</b> - <small><i>{{$lastcom->created_at->format('d M Y')}}</i></small>
                </p>
                {{-- <div class="alert alert-dark" style="background: #f0f1f2; color:#383d41">
                <b>{{$lastcom->user->name}}</b> 
                <i>{{$lastcom->created_at->format('d M Y')}}</i>
                <p>
                    {{$lastcom->message}}
                </p>
                </div> --}}
                @endif
                <div>
                <form id="postComment">
                    @csrf
                    <div class="form-group">
                        <textarea name="message" id="message" rows="4" class="form-control" placeholder="Write e comment"></textarea>
                        <button class="btn btn-sm btn-info m-t-10">Post</button>
                    </div>
                </form>
                </div>
            </div>

            @if($invoice->receipt && $invoice->status)
            <div class="col-md-{{($invoice->status && in_array(auth()->id(), $setting->admins)) ? '6' : '12'}}">
                <div class="form-group" align="center">
                    <label>Payment Receipt</label>
                    <p>
                        @if(pathinfo($invoice->receipt, PATHINFO_EXTENSION) =='pdf')
                        <a href="javascript:;" id="previewReceipt" data-toggle="modal" id="previewReceipt" data-target="#previewModal">Open PDF</a>
                        @else
                        <a href="javascript:;" id="previewReceipt" data-toggle="modal" id="previewReceipt" data-target="#previewModal"><img src="{{ asset('user-uploads/'.$invoice->receipt) }}" alt="{{$invoice->name}}" height="200px" />
                        </a>
                        @endif
                    </p>
                </div>
            </div>
            @endif

            @if($invoice->status && in_array(auth()->id(), $setting->admins))
            <div class="col-md-6">
                <form id="uploadReceiptForm">
                    @csrf
                    <div class="form-group btn-group">
                        <label>Upload Receipt</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    <div class="form-group">
                        <button class="btn btn-sm btn-success">Upload</button>
                    </div>
                </form>
            </div>
            @endif

            @if(!$invoice->status && !$invoice->review)
            <div class="col-md-12">
                <p class="text-danger">Waiting for <a target="_blank" href="{{route('admin.employees.show', $invoice->reviewer_id)}}">{{$invoice->checkedBy->name}}</a> to check the Invoice.</p>
            </div>
            @endif

            @if(auth()->id() == $invoice->reviewer_id && !$invoice->processed)
            <div class="col-md-6">
                <form id="changeReviewStatus">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        @if($invoice->review)
                        <input type="hidden" name="status" value="0" id="status">
                        <button class="btn btn-sm btn-danger" id="cancelPayment">Cancel Payment Request</button>
                        @else
                        <input type="hidden" name="status" value="1" id="status">
                        <button class="btn btn-sm btn-success" id="proceedPayment">Proceed for Payment</button>
                        @endif
                    </div>
                </form>
            </div>
            @endif

             @if(in_array(auth()->id(), $setting->admins) && $invoice->review && !$invoice->status)
             <div class="col-md-6">
                <form id="proceedRequest">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        @if($invoice->processed)
                        <input type="hidden" name="status" value="0" id="status">
                        <button class="btn btn-sm btn-danger" id="cancelPay">Cancel Pay Request</button>
                        @else
                        <div class="form-group">
                            <label class="required">Select</label>
                            <select class="form-control" name="financer" id="financer">
                                @foreach($financers as $financer)
                                    <option value="{{ $financer->id }}">{{ $financer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="status" value="1" id="status">
                        <button class="btn btn-sm btn-success" id="proceedPay">Proceed to Pay</button>
                        @endif
                    </div>
                </form>
            </div>
            @endif

            @if(in_array(auth()->id(), $setting->admins) && $invoice->review && $invoice->processed)
            <form method="post" id="statusChangeForm">
                @csrf
                @method('PUT')
                @if(!$invoice->status)
                <div class="col-md-12 m-t-10">
                    <div class="form-group btn-group">
                        <label>Upload Receipt</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png" id="receiptFile">
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="form-group btn-group">
                        @if(!$invoice->status)
                        <button class="btn btn-success btn-sm" id="status-update" data-status="paid">Change to Paid</button>
                        @else
                        <button class="btn btn-danger btn-sm m-t-10" id="status-update" data-status="unpaid">Change to Unpaid</button>
                        @endif
                    </div>
                </div>

                <input type="hidden" name="status" value="" id="statusValue">
            </form>
            @endif
        </div>

    </div>

    <div id="comments" class="tab-pane fade">
        <div class="steamline">
            @foreach($invoice->comments as $comment)
            <div class="sl-item">
                <div class="sl-left" style="margin-left: -13px !important;"><img class="img-circle" src="@if($comment->user->image !=null) /user-uploads/avatar/{{$comment->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="">
                </div>
                <div class="sl-right">
                    <div>
                        <h6><b>{{$comment->user->name}}</b></h6>
                        <p>{{$comment->message}}</p>
                        <span class="sl-date">{{$comment->created_at->format('d-m-Y H:s a')}}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div id="activity" class="tab-pane fade">
        <div class="steamline">
            @foreach($invoice->activity as $log)
            <div class="sl-item">
                <div class="sl-left" style="margin-left: -13px !important;"><img class="img-circle" src="@if($log->user->image !=null) /user-uploads/avatar/{{$log->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="">
                </div>
                <div class="sl-right">
                    <div>
                        <h6><b>{{$log->user->name}}</b> {{$log->message}}</h6>
                        <span class="sl-date">{{$log->created_at->format('d-m-Y H:s a')}}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
</div>

<script type="text/javascript">
    @if(in_array(auth()->id(), $setting->admins))
    $(function () {
        $('body').on('click', '#status-update', function (e) {
            e.preventDefault();
            status = $(this).data('status');
            $('#statusValue').val(status);

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
                content: "input",
            }).then(function (isConfirm) {

                if (isConfirm =='') {
                    $.toast({
                        text: 'Type your password first!',
                        position: 'top-right',
                        loaderBg:'#ff6849',
                        icon: 'error',
                        hideAfter: 3500
                    });

                    return false;
                } else if (isConfirm == null) { return false; }

                @if($user->hasRole('admin'))
                url = '{{route('admin.outreach-invoices.status-update', $invoice->id)}}';
                @else
                url = '{{route('member.outreach-invoices.status-update', $invoice->id)}}';
                @endif

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    file: true,
                    container: '#statusChangeForm',
                    data: {'password': isConfirm},
                    success: function(res){
                        if (res.status == 'success') {
                            window.LaravelDataTables["invoices-table"].draw();
                            reloadInvoice();
                        }
                    }
                });
            });
        });

    });
    @endif

    function reloadInvoice(){
        @if($user->hasRole('admin'))
        url = '{{route('admin.outreach-invoices.show', $invoice->id)}}';
        @else
        url = '{{route('member.outreach-invoices.show', $invoice->id)}}';
        @endif

        $('#invoiceModal .modal-body').html('Loading...');

        $.get(url, function(data, status){
            $('#invoiceModal .modal-content').html(data);
        });
    }

    $('#uploadReceiptForm').submit(function(e){
        e.preventDefault();

        @if($user->hasRole('admin'))
        url = '{{route('admin.outreach-invoices.receipt', $invoice->id)}}';
        @else
        url = '{{route('member.outreach-invoices.receipt', $invoice->id)}}';
        @endif

        $.easyAjax({
            url: url,
            container: '#uploadReceiptForm',
            type: "POST",
            file: true,
            success: function(res){
                reloadInvoice();
            }
        })
    })

    $('#previewReceipt').click(function(){
        url = '{{route('member.article.receiptDownload', [$invoice->id, ':id'])}}';
        url = url.replace(':id', $(this).data('id'));
        ext = '{{pathinfo($invoice->receipt, PATHINFO_EXTENSION)}}';

        if (ext =='pdf') {
            $('#previewModal').find('.modal-body').html('<iframe src="{{asset('laraview/#../user-uploads/'.$invoice->receipt)}}" width="100%" height="500px"></iframe>');
        } else {
            $('#previewModal').find('.modal-body').html('<img src="{{ asset('user-uploads/'.$invoice->receipt) }}" id="previewImage" style="width: 100%; height: cover;">');
        }
    })

    $('#invoiceModal #link-status').change(function(){
        id = $(this).data('id');
        status = $(this).val();

        if (status == 'rejected') {
            $(this).next().toggle('show');
        } else {
            $(this).next().hide();

            linkStatus(id, status);
        }
    });

    $('#invoiceModal #link-reject-cancel').click(function(){
        $(this).parent().toggle('hide');
        $(this).parent().prev().val($(this).data('status')).change(function(e){ e.preventDefault(); });
    });

    $('#invoiceModal #link-reject').click(function(){
        id = $(this).data('id');
        message = $(this).parent().find('textarea').val();

        if (message =='') {
            $.toast({
                text: 'Please enter reason for rejection!',
                position: 'top-right',
                loaderBg:'#ff6849',
                icon: 'error',
                hideAfter: 3500
            });
            $('#link-remarks').css('border', '1px solid #dc3545');
            return false;
        }

        linkStatus(id, status, message);
        $(this).parent().toggle('hide');
    })

    function reloadInvoice(){
        $('#invoiceModal .modal-body').html('Loading...');
        url = '{{route('member.outreach-invoices.show', $invoice->id)}}';
        $.get(url, function(res){
            $('#invoiceModal .modal-content').html(res);
        });
    }

    function linkStatus(id, status, message = null){
        url = '{{route('member.outreach-backlinks.status-update', ':id')}}';
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'status': status, 'message': message, '_method': 'PUT', '_token': '{{csrf_token()}}'},
            success: function(res){
                if (res.status == 'success') {
                    reloadInvoice();
                }
            }
        })
    }

    $('#changeReviewStatus').submit(function(e){
        e.preventDefault();
        url = '{{route('member.outreach-invoices.review', $invoice->id)}}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            success: function(response){
                reloadInvoice();
            }
        })
    })

    $('#proceedRequest').submit(function(e){
        e.preventDefault();
        url = '{{route('admin.outreach-invoices.proceed', $invoice->id)}}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            success: function(response){
                reloadInvoice();
            }
        })
    })

    $('#postComment').submit(function(e){
        e.preventDefault();
        url = '{{route('member.outreach-invoices.comment', $invoice->id)}}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            success: function(response){
                reloadInvoice();
            }
        })
    })

    //Copy link
    function copyLink(){
        var url = '{{route('member.outreach-invoices.index')}}?view-invoice={{$invoice->id}}';
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();

        $.showToastr('Coppied!','success', {'showDuration': '20', 'hideDuration': '0', 'timeOut': '300'});
    }
</script>