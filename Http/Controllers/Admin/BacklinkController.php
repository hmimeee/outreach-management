<?php

namespace Modules\OutreachManagement\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use Modules\OutreachManagement\Entities\OutreachActivity;
use App\Helper\Reply;
use Modules\OutreachManagement\Entities\Backlink;
use Modules\OutreachManagement\Entities\Site;
use Modules\OutreachManagement\Datatables\BacklinksDataTable;
use App\Project;
use Modules\OutreachManagement\Http\Requests\StoreBacklinkRequest;
use Modules\OutreachManagement\Http\Requests\UpdateBacklinkRequest;
use Modules\OutreachManagement\Entities\OutreachSetting;
use Modules\OutreachManagement\Entities\OutreachComment;
use Illuminate\Support\Facades\Notification;
use Modules\OutreachManagement\Notifications\BacklinkNotification;
use App\User;

class BacklinkController extends AdminBaseController
{
    public $setting;
    
    public function __construct()
    {
        parent::__construct();
        $this->setting = OutreachSetting::first();
    }

    /**
     * Add activity log.
     * @return none
     */
    public function addLog($id, $message)
    {
        OutreachActivity::create([
            'outreach_backlink_id' => $id,
            'user_id' => auth()->id(),
            'type' => 'backlink',
            'message' => $message,
        ]);
    }

    /**
     * Add comment.
     * @return none
     */
    public function addComment($id, $message)
    {
        OutreachComment::create([
            'outreach_backlink_id' => $id,
            'user_id' => auth()->id(),
            'message' => $message,
        ]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(BacklinksDataTable $dataTable)
    {
        $this->pageTitle = 'Outreach Backlinks Management';
        $this->pageIcon = 'ti-share-alt';
        $this->totalBacklinks = Backlink::all()->count();

        return $dataTable->render('outreachmanagement::admin.backlinks', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->projects = Project::whereNotIn('status', ['canceled', 'finished'])->get();
        $this->sites = Site::where('status', 'approved')->get();

        if (!in_array(auth()->id(), $this->setting->maintainers)) {
            return view('outreachmanagement::error', ['message' => 'You can not add anything!']);
        }

        return view('outreachmanagement::backlinks.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreBacklinkRequest $request
     * @return Response
     */
    public function store(StoreBacklinkRequest $request)
    {
        if (!$request->paid) {
            unset($request['outreach_site_id']);
        } else {
            $site = Site::find($request->outreach_site_id);
            $request['cost'] = $request->type =='post' ? $site->post_price : $site->link_price;
        }

        if ($request->paid && !($request->cost > 0)) {
            return Reply::error('Please update the post/link price of the website!');
        }

        for ($i=0; $i < count($request->project_id); $i++) {
            $backlinkSite = str_replace('www.', '', parse_url($request->backlink[$i])['host']);
            if ($site->site != $backlinkSite) {
                return Reply::error('The backlink website: '.$backlinkSite.', doesn\'t match to the selected website');
            }
        }

        for ($i=0; $i < count($request->project_id); $i++) {
            try {
                $backlink = Backlink::create([
                    'project_id' => $request->project_id[$i],
                    'outreach_site_id' => $request->outreach_site_id,
                    'website' => $request->website,
                    'backlink' => $request->backlink[$i],
                    'url' => $request->url[$i],
                    'type' => $request->type,
                    'published_date' => date('Y-m-d'),
                    'paid' => $request->paid,
                    'cost' => $request->cost,
                ]);

                //Add activity log
                $this->addLog($backlink->id, 'added the backlink details');

            } catch (Exception $e) {
                $errors[] = $e;
            }
        }

        return Reply::success('Backlinks added successfully!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->backlink = Backlink::findOrFail($id);
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not view anything!']);
        }

        return view('outreachmanagement::backlinks.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->backlink = Backlink::findOrFail($id);
        $this->projects = Project::whereNotIn('status', ['canceled', 'finished'])->get();
        $this->sites = Site::where('status', 'approved')->get();
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not edit anything!']);
        }

        return view('outreachmanagement::backlinks.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateBacklinkRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateBacklinkRequest $request, $id)
    {
        try {

            if (!$request->has('paid')) {
                $request['paid'] = 0;
                $request['cost'] = 0;
                $request['outreach_site_id'] = null;
            } else {
                $site = Site::find($request->outreach_site_id);
                $request['cost'] = $request->type =='post' ? $site->post_price : $site->link_price;
            }

            if (!$request->has('indexed')) {
                $request['indexed'] = 0;
            }

            if ($request->remarks != null || $request->remarks != '') {
                $message = $request->remarks;
                unset($request['remarks']);
            }

            if (in_array(auth()->id(), $this->setting->maintainers)) {
                $request['status'] = 'pending';
            }

            $backlink = Backlink::findOrFail($id);
            $backlink->update($request->all());

            if (isset($message)) {
                //Add comment
                $this->addComment($backlink->id, $message);
            }

            //Add activity log
            $this->addLog($backlink->id, 'updated the backlink details');

            //Send the notification
            $notifyTo = User::find($this->setting->maintainers);
            Notification::send($notifyTo, new BacklinkNotification($backlink, 'Outreach backlink updated', 'updated the outreach backlink.'));

        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Backlink details updated successfully!');
    }

    /**
     * Update the specified field of a row.
     * @param Request $request
     * @param int $site
     * @return Response
     */
    public function status(Request $request, Backlink $backlink)
    {
        //Check if the status parameter is available in the $request
        $request->validate(['status' => 'required']);

        try {
            //Update backlink status
            $backlink->status = $request->status;
            if ($request->remarks) {
                $backlink->remarks = $request->remarks;
            }
            $backlink->save();

            //Add comment
            if ($request->message != null) {
                $this->addComment($backlink->id, $request->message);
            }

            if ($request->status == 'rejected') {
                //Send the notification
                $notifyTo = User::find($this->setting->maintainers);
                Notification::send($notifyTo, new BacklinkNotification($backlink, 'Outreach backlink rejected', 'rejected the outreach backlink.'));
            }

            //Add activity log
            $this->addLog($backlink->id, $request->status .' the backlink');
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Backlink status updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $backlink = Backlink::findOrFail($id);

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return Reply::error('You can not delete the backlink!');
        }

        if ($backlink->invoice) {
            return Reply::error('The backlink has an invoice, delete that first!');
        }

        try {
            //Send the notification
            $notifyTo = User::find($this->setting->maintainers);
            Notification::send($notifyTo, new BacklinkNotification($backlink, 'Outreach backlink deleted', 'deleted the outreach backlink.', true));

            $backlink->delete();
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Backlink deleted successfully!');
    }
}
