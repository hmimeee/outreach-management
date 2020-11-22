<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <i class="ti-id-badge"></i> Backlink Information
        <a href="javascript:;" onclick="copyLink()" class="btn btn-xs btn-default m-l-10"><i class="fa fa-link"></i></a>
    </h4>
</div>
<div class="modal-body">

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#information">Information</a></li>
        <li><a data-toggle="tab" href="#activity">Activity</a></li>
    </ul>

    <div class="tab-content">
        <div id="information" class="tab-pane fade in active">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Bcklink</label>
                        <p>
                            <a target="_blank" href="{{ $backlink->backlink }}">{{ $backlink->backlink }}</a>
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Project</label>
                        <p>
                            <a target="_blank" href="{{auth()->user()->hasRole('admin') ? route('admin.projects.show', $backlink->project_id) : route('member.projects.show', $backlink->project_id) }}">{{ $backlink->project->project_name }}</a>
                        </p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Website</label>
                        <p>
                            @if($backlink->paid)
                            <a target="_blank" href="{{auth()->user()->hasRole('admin') ? route('admin.outreach-management.index') : route('member.outreach-management.index') }}?view-site={{$backlink->outreach_site_id}}">{{ $backlink->site->site }}</a>
                            @else
                            <a target="_blank" href="{{ $backlink->website }}">{{ $backlink->website }}</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Published Date</label>
                        <p>{{ $backlink->published_date }}</p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Type</label>
                        <p><label class="label label-primary">{{ ucfirst($backlink->type) }}</label></p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Cost</label>
                        <p>{{ $backlink->paid == 1 ? '$'.$backlink->cost: 'Free' }}</p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Indexed</label>
                        <p>
                            {!! $backlink->indexed ? '<label class="label label-success">Yes' : '--' !!}</label>
                        </p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <p>
                            @php($class = ['rejected' => 'danger', 'approved' => 'success', 'pending' => 'info'])

                            {!! '<span class="label label-'.$class[$backlink->status].'">'.ucwords($backlink->status).'</span>' !!}
                        </p>
                    </div>
                </div>

                <div class="col-md-12 m-t-10">
                    <div class="form-group">
                        <label>Remarks</label>
                        @php($comment = $backlink->comments->last())
                        @if($comment)
                        <p>
                            <img class="img-circle" src="@if($comment->user->image !=null) /user-uploads/avatar/{{$comment->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="" data-toggle="tooltip" data-placement="top" title="{{$comment->user->name}}"> {{$comment->message ?? ''}}
                        </p>
                        @else
                        <p>
                            {!! $backlink->remarks !!}
                        </p>
                        @endif
                    </div>
                </div>

                @if(in_array(auth()->id(), array_merge($setting->observers, $setting->admins)))
                <div class="col-md-12 m-t-10">
                    <div class="form-group btn-group">
                        @if($backlink->status !='approved')
                        <button class="btn btn-success btn-sm" id="status-update" data-status="approved">Approve</button>
                        @endif
                        @if($backlink->status !='rejected')
                        <button class="btn btn-danger btn-sm reject" id="status-update" data-status="rejected">Reject</button>
                        @endif
                    </div>

                    <div id="commentMessageBox" style="display: none;">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="message" class="form-control" id="commentMessage" placeholder="Type the reason behind" rows="5" maxlength="255"></textarea>
                        </div>
                        <div class="form-group btn-group">
                            <button class="btn btn-danger btn-sm" id="status-update" data-status="rejected" data-action="main">Reject</button>
                            <button class="btn btn-inverse btn-sm" id="cancel-rejection">Cancel</button>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        <div id="activity" class="tab-pane fade">
            <div class="steamline">
                @foreach($backlink->activity->sortByDesc('id') as $log)
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

<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">
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

    $('body #status-update').click(function(){
        status = $(this).data('status');
        action = $(this).data('action');

        if (status ==='rejected' && action !=='main') {
            $(this).toggle('hide');
            $('#commentMessageBox').toggle('show');
            return false;
        }

        if (action ==='main' && $('#commentMessage').val() === '') {
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

        url = '{{route('admin.outreach-backlinks.status-update', $backlink->id)}}';

        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'status': status, 'remarks': $('#remarks').val(), 'message': $('#commentMessage').val(), '_token': '{{csrf_token()}}', '_method': 'PUT'},
            success: function(res){
                if (res.status == 'success') {
                    window.LaravelDataTables["links-table"].draw();
                    $('#backlinkModal').modal('toggle');
                }
            }
        });
    })

    $('body #cancel-rejection').click(function(){
        $('#commentMessageBox').toggle('hide');
        $('.reject').toggle('show');
    })

    //Copy link
    function copyLink(){
        var url = '{{route('member.outreach-backlinks.index')}}?view-link={{$backlink->id}}';
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();

        $.showToastr('Coppied!','success', {'showDuration': '20', 'hideDuration': '0', 'timeOut': '300'});
    }
</script>