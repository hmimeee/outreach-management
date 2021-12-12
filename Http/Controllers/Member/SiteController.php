<?php

namespace Modules\OutreachManagement\Http\Controllers\Member;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\OutreachManagement\Entities\Site;
use Modules\OutreachManagement\Entities\OutreachActivity;
use Modules\OutreachManagement\Datatables\SitesDataTable;
use Modules\OutreachManagement\Http\Requests\StoreSiteRequest;
use App\Helper\Reply;
use Modules\OutreachManagement\Entities\OutreachSetting;
use Modules\OutreachManagement\Http\Requests\UpdateSiteRequest;
use Illuminate\Support\Facades\Notification;
use Modules\OutreachManagement\Notifications\SiteNotification;
use Modules\OutreachManagement\Entities\OutreachComment;
use App\User;

class SiteController extends MemberBaseController
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
            'outreach_site_id' => $id,
            'user_id' => auth()->id(),
            'type' => 'site',
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
            'outreach_site_id' => $id,
            'user_id' => auth()->id(),
            'message' => $message,
        ]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(SitesDataTable $dataTable)
    {
        if (!in_array(auth()->id(), array_merge($this->setting->admins, $this->setting->maintainers, $this->setting->observers))) {
            return abort(403);
        }
        
        $this->pageTitle = 'Outreach Management';
        $this->pageIcon = 'ti-share-alt';
        $this->totalSites = Site::all()->count();

        return $dataTable->render('outreachmanagement::member.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!in_array(auth()->id(), $this->setting->maintainers)) {
            return view('outreachmanagement::error', ['message' => 'You can not add anything!']);
        }

        return view('outreachmanagement::sites.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(StoreSiteRequest $request)
    {
        try {
            //Add site details
            $site = Site::create($request->all());

            //Add activity log
            $this->addLog($site->id, 'added the website details');

            //Send the notification
            $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new SiteNotification($site, 'Outreach website approval request', 'requested the outreach website for approval.'));

        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Website added successfully!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->site = Site::findOrFail($id);
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not view anything!']);
        }

        return view('outreachmanagement::sites.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->site = Site::findOrFail($id);
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not edit anything!']);
        }

        return view('outreachmanagement::sites.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateSiteRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateSiteRequest $request, $id)
    {
        //Update site details
        $site = Site::find($id);

        if (in_array(auth()->id(), array_merge($this->setting->maintainers, $this->setting->admins))) {

            $request['status'] = 'pending';
            $site->update($request->except(['website']));

                //Add activity log
            $this->addLog($site->id, 'updated the website details');

            //Send the notification
            $notifyTo = User::find($this->setting->admins);
            Notification::send($notifyTo, new SiteNotification($site, 'Outreach website details updated', 'updated the outreach website details.'));

        } else {
            return Reply::error('You have no access to update details!');
        }

        return Reply::success('Website added successfully!');
    }

    /**
     * Update the specified field of a row.
     * @param Request $request
     * @param int $site
     * @return Response
     */
    public function status(Request $request, Site $site)
    {
        try {
            //Update site details
            $site->update(['status' => $request->status]);

            //Add activity log
            $this->addLog($site->id, $request->status.' the website for guest post');

            //Send the notification
            $notifyTo = User::find($this->setting->maintainers);
            Notification::send($notifyTo, new SiteNotification($site, 'Outreach website status updated', 'updated the outreach website status.'));
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Website status updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        if (in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->admins)) {
            //
        } else {
            return Reply::error('You can not delete the website!');
        }

        $site = Site::find($id);

        if ($site->status == 'approved') {
            return Reply::error('You can delete approved websites!');
        }

        if ($site->invoices->count() > 0) {
            return Reply::error('The website has some invoices, delete them first!');
        }

        //Send the notification
        $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
        $notifyTo = User::find($notifyTo);
        Notification::send($notifyTo, new SiteNotification($site, 'Outreach website deleted', 'deleted the outreach website.', true));

        if ($site->delete()) {
            return Reply::success('Website deleted successfully!');
        }
    }
}
