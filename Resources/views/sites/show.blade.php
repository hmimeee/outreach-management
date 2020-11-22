<style type="text/css">
    .border-end {
        border-top: 1px solid rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(0,0,0,0.2);
    }

    .right-border {
        border-right: 1px solid rgba(0,0,0,0.2);
        border-top: 1px solid rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(0,0,0,0.2);
    }

    .left-border {
        border-left: 1px solid rgba(0,0,0,0.2);
        border-top: 1px solid rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(0,0,0,0.2);
    }

    .block {
        height: 108px;
        padding: 2px;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <i class="ti-id-badge"></i> Website Information
        <a href="javascript:;" onclick="copyLink()" class="btn btn-xs btn-default m-l-10"><i class="fa fa-link"></i></a>
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
                <div class="col-md-12">
                    <div class="form-group">
                        <h3>{!! '<b>'.$site->niche.'</b>' ?? '<i>No niche</i>' !!}</h3>
                        <p>
                            <a href="{{ $site->website }}" target="_blank">{{ $site->website }}</a>
                            @php($class = ['rejected' => 'danger', 'approved' => 'success', 'soft rejected' => 'warning', 'pending' => 'info'])

                            {!! '<span class="btn cursor-pointer btn-xs btn-'.$class[$site->status].'">'.ucwords($site->status).'</span>' !!}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 right-border block" align="center">
                    <label>Domain Rating</label>
                    <div id="domainRating"></div>
                </div>
                <div class="col-md-2 right-border block" align="center">
                    <label>Spam Score</label>
                    <div id="spamScore"></div>
                </div>
                <div class="col-md-2 right-border block" align="center">
                    <div class="form-group">
                        <label>Traffic (Monthly)</label>
                        <h2 class="text-info"><b>{{ $site->traffic ?? '--' }}</b></h2>
                    </div>
                </div>
                <div class="col-md-2 right-border block" align="center">
                    <div class="form-group">
                        <label>Traffic Value</label>
                        <h2 class="text-info"><b>{{ $site->traffic_value ? '$'.$site->traffic_value : '--' }}</b></h2>
                    </div>
                </div>
                <div class="col-md-2 right-border block" align="center">
                    <div class="form-group">
                        <label>Post Price (Each)</label>
                        <h2 class="text-info">
                            <b> {{ $site->post_price ? '$'.$site->post_price : '--' }} </b>
                        </h2>
                    </div>
                </div>
                <div class="col-md-2 border-end block" align="center">
                    <div class="form-group">
                        <label>Link Price (Each)</label>
                        <h2 class="text-info">
                            <b> {{ $site->link_price ? '$'.$site->link_price : '--' }} </b>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="row m-t-20">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ahref Link</label>
                        <p>
                            @if($site->ahref_link)
                            <a href="{{ $site->ahref_link }}" target="_blank">Visit Link</a>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ahref Snap (Screenshot)</label>
                        <p>
                            @if($site->ahref_snap)
                            <a href="{{ $site->ahref_snap }}" target="_blank">Visit Link</a>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Notes</label>
                        @php($comment = $site->comments->last())
                        @if($comment)
                        <p>
                            <img class="img-circle" src="@if($comment->user->image !=null) /user-uploads/avatar/{{$comment->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="" data-toggle="tooltip" data-placement="top" title="{{$comment->user->name}}"> {{$comment->message ?? ''}}
                        </p>
                        @else
                        <p>
                            {!! $site->notes !!}
                        </p>
                        @endif
                    </div>
                </div>

                @if(in_array(auth()->id(), $setting->admins))
                <div class="col-md-12 m-t-10" id="statusUpdateTab">
                    <div class="form-group btn-group">
                        @if($site->status !='approved')
                        <button class="btn btn-success btn-sm" id="status-update" data-status="approved" data-type="main">Approve</button>
                        @endif
                        @if($site->status !='soft rejected')
                        <button class="btn btn-warning btn-sm" id="status-update" data-status="soft rejected" data-type="main">Soft Reject</button>
                        @endif
                        @if($site->status !='rejected')
                        <button class="btn btn-danger btn-sm" id="status-update" data-status="rejected" data-type="main">Reject</button>
                        @endif
                    </div>
                </div>
                <div class="col-md-12 m-t-10" id="commentMessageBox" style="display: none;">
                    <div class="form-group">
                        <label>Note</label>
                        <textarea name="message" class="form-control" id="commentMessage" placeholder="Type a note" rows="5" maxlength="255"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info btn-sm" id="status-update" data-status="soft rejected">Submit</button>
                        <a href="javascript:;" class="btn btn-inverse btn-sm" id="cancelStatusUpdate">Cancel</a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div id="comments" class="tab-pane fade">
            <div class="steamline">
                @foreach($site->comments->sortByDesc('id') as $comment)
                <div class="sl-item">
                    <div class="sl-left" style="margin-left: -13px !important;"><img class="img-circle" src="@if($comment->user->image !=null) /user-uploads/avatar/{{$comment->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="">
                    </div>
                    <div class="sl-right">
                        <div>
                            <h6><b>{{$comment->user->name}}</b></h6>
                            <h6>{{$comment->message}}</h6>
                            <span class="sl-date">{{$comment->created_at->format('d-m-Y H:s a')}}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div id="activity" class="tab-pane fade">
            <div class="steamline">
                @foreach($site->activity as $log)
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

<script src="{{ asset('plugins/bower_components/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/peity/jquery.peity.init.js') }}"></script>
@if(in_array(auth()->id(), $setting->admins))
<script type="text/javascript">
    $('body #status-update').click(function(){
        status = $(this).data('status');
        type = $(this).data('type');
        style = $(this).attr('class');
        message = $('#commentMessage').val();
        url = '{{route('admin.outreach-management.status-update', $site->id)}}';

        if (type ==='main') {
            $('#statusUpdateTab').toggle('hide');
            $('#commentMessageBox').toggle('show');
            $('#commentMessageBox').find('button').attr('data-status', status);
            $('#commentMessageBox').find('button').attr('class', style);
            $('#commentMessageBox').find('button').text($(this).text());
            return false;
        } else if (type ==='main') {
            $('#commentMessageBox').hide();
        }

        if ($('#commentMessage').val() === '' && type !=='main' && status !=='approved') {
            $.toast({
                text: 'Type a note first!',
                position: 'top-right',
                loaderBg:'#ff6849',
                icon: 'error',
                hideAfter: 3500
            });
            $('#commentMessage').css('border', '1px solid #dc3545');
            return false;
        }

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'status': status, '_token': '{{csrf_token()}}', '_method': 'PUT', 'message': message},
            success: function(res){
                if (res.status == 'success') {
                    window.LaravelDataTables["sites-table"].draw();
                    $('#siteModal').modal('toggle');
                }
            }
        });
    })

    $('#cancelStatusUpdate').click(function(){
        $('#statusUpdateTab').toggle('show');
        $('#commentMessageBox').toggle('hide');
    })
</script>
@endif

<script src="{{asset('js/circles.min.js')}}"></script>
<script>
    function makeCircle(id, value, color = '#00c292'){
        return Circles.create({
            id:           id,
            value:        value,
            radius:       35,
            width:        12,
            duration:     1,
            colors:       ['#dedede', color]
        });
    }

    makeCircle('domainRating', '{{$site->domain_rating}}');
    makeCircle('spamScore', '{{$site->spam_score}}', '#fb9678');

    //Copy link
    function copyLink(){
        var url = '{{route('member.outreach-management.index')}}?view-site={{$site->id}}';
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();

        $.showToastr('Coppied!','success', {'showDuration': '20', 'hideDuration': '0', 'timeOut': '300'});
    }
</script>