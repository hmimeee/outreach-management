<?php

namespace Modules\OutreachManagement\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Helper\Reply;
use Modules\OutreachManagement\Entities\OutreachActivity;
use Modules\OutreachManagement\Entities\OutreachSetting;
use App\User;
use Modules\OutreachManagement\Http\Requests\UpdateSettingRequest;

class SettingController extends AdminBaseController
{
    public $setting;
    
    public function __construct()
    {
        parent::__construct();
        $this->setting = OutreachSetting::first();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->pageTitle = 'Outreach Management Settings';
        $this->users = User::where('status', 'active')->get();
        $this->data['setting'] = $this->setting;

        return view('outreachmanagement::admin.settings', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('outreachmanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('outreachmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('outreachmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateSettingRequest $request
     * @return Response
     */
    public function update(UpdateSettingRequest $request)
    {
        $setting = OutreachSetting::first();
        try {
            if ($setting != null) {
                $setting->update([
                    'admins' => $request->admins,
                    'maintainers' => $request->maintainers,
                    'observers' => $request->observers,
                    'financers' => $request->financers,
                ]);
            } else {
                $setting = OutreachSetting::create([
                    'admins' => $request->admins,
                    'maintainers' => $request->maintainers,
                    'observers' => $request->observers,
                    'financers' => $request->financers,
                ]);
            }
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Settings updated successfully!');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateModule(Request $request)
    {
        //Adding bulk data code
        // $data = $request->file('package')->move(public_path('user-uploads/temp'), 'file.csv');
        // $data = file($data);
        // for ($i=1; $i < count($data); $i++) {
        //     $site = str_getcsv($data[$i]);

        //     $ok [] = Site::create([
        //         'website' => $site[0],
        //         'post_price' => $site[1],
        //         'niche' => $site[2],
        //         'traffic' => $site[3],
        //         'traffic_value' => $site[4],
        //         'domain_rating' => $site[5],
        //         'spam_score' => $site[6],
        //         'ahref_snap' => $site[7],
        //         'ahref_link' => $site[8],
        //     ]);
        // }
        // dd($ok);

        if($request->hasFile('package')) {

            $fileData = $request->file('package');
            $filename = $fileData->getClientOriginalName();
            $fileData->move(public_path('user-uploads/temp'), $filename);

            if(substr($filename,0,20) != 'OutreachManagement_v'){
                unlink(public_path('user-uploads/temp/').$filename);
                return Reply::error('Unsupported package!');
            }

            $zip = new \ZipArchive;
            $res = $zip->open(public_path('user-uploads/temp/').$filename);
            if ($res === TRUE) {
                $zip->extractTo(base_path('/Modules/'));
                $zip->close();
                unlink(public_path('user-uploads/temp/').$filename);
                return Reply::success('Successfully updated!');
            }

            unlink(public_path('user-uploads/temp/').$filename);
            return Reply::error('Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
