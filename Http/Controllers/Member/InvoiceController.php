<?php

namespace Modules\OutreachManagement\Http\Controllers\Member;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Member\MemberBaseController;
use Modules\OutreachManagement\Datatables\InvoicesDataTable;
use Modules\OutreachManagement\Entities\Invoice;
use Modules\OutreachManagement\Entities\Site;
use App\Helper\Reply;
use Illuminate\Support\Facades\Notification;
use Modules\OutreachManagement\Notifications\InvoiceNotification;
use Modules\OutreachManagement\Http\Requests\StoreInvoiceRequest;
use Modules\OutreachManagement\Entities\OutreachActivity;
use Modules\OutreachManagement\Entities\OutreachSetting;
use App\User;
use Modules\OutreachManagement\Http\Requests\UpdateInvoiceRequest;

class InvoiceController extends MemberBaseController
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
            'outreach_invoice_id' => $id,
            'user_id' => auth()->id(),
            'type' => 'invoice',
            'message' => $message,
        ]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(InvoicesDataTable $dataTable)
    {
        if (!isset($this->setting->admins) || !isset($this->setting->maintainers) || !isset($this->setting->observers)) {
            return redirect()->back()->withErrors('Please update the settings first!');
        }

        if (!in_array(auth()->id(), array_merge($this->setting->admins, $this->setting->maintainers, $this->setting->observers))) {
            return abort(403);
        }

        $this->pageTitle = 'Outreach Invoice Management';
        $this->pageIcon = 'ti-share-alt';
        $this->totalInvoices = Invoice::all()->count();

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return redirect()->back()->withErrors('You can not visit the page!');
        }

        return $dataTable->render('outreachmanagement::member.invoices', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->sites = Site::where('status', 'approved')->get();

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not generate invoice!']);
        }

        return view('outreachmanagement::invoices.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreInvoiceRequest $request
     * @return Response
     */
    public function store(StoreInvoiceRequest $request)
    {
        $site = Site::findOrFail($request->outreach_site_id);
        $cost = 0;
        $links = $site->backlinks->where('paid', 1)->where('outreach_invoice_id', null);
        foreach ($links as $link) {
            $cost += $link->cost;
        }

        if ($cost ==0) {
            return Reply::error('There is no backlinks for payment!');
        } else {
            $request['amount'] = $cost;
        }

        try {
            //Temporary name
            $request['name'] = rand();

            //Generate invoice
            $invoice = Invoice::create($request->all());

            //Changeing name with ID
            $invoice->name = 'BL'.str_pad($invoice->id, 8, '0', STR_PAD_LEFT);
            $invoice->save();

            foreach ($links as $link) {
                $link->update(['outreach_invoice_id' => $invoice->id]);
            }

            //Add activity log
            $this->addLog($invoice->id, 'generated the invoice');

            //Send the notification
            $notifyTo = array_unique(array(auth()->id(), $invoice->reviewer_id));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new InvoiceNotification($invoice, 'Outreach invoice generated', 'generated the outreach invoice.'));
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }
        return Reply::success('Invoice generated successfully!');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not view the invoice!']);
        }

        return view('outreachmanagement::invoices.show', $this->data);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function print($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->data['setting'] = $this->setting;

        if (in_array(auth()->id(), $this->setting->admins) || in_array(auth()->id(), $this->setting->maintainers) || in_array(auth()->id(), $this->setting->observers)) {
            //
        } else {
            return view('outreachmanagement::error', ['message' => 'You can not view the invoice!']);
        }

        return view('outreachmanagement::invoices.print', $this->data);
    }

    /**
     * Show the specified resource.
     * @param int $site
     * @return Response
     */
    public function data(Site $site)
    {
        $this->links = $site->backlinks->where('paid', 1)->where('outreach_invoice_id', null);
        $this->reviewers = User::find($this->setting->observers);
        $cost = 0;

        foreach ($this->links as $link) {
            $cost += $link->cost;
        }

        $this->cost = $cost;

        if ($cost > 0) {
            $data = view('outreachmanagement::invoices.data', $this->data)->render();
        } else {
            $data = '<div class="alert alert-danger">There is no backlinks for invoice!</div>';
        }


        return Reply::dataOnly(['html' => $data, 'cost' => $cost]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $this->invoice = Invoice::findOrFail($id);
        $this->sites = Site::where('status', 'approved')->get();
        $this->reviewers = User::find($this->setting->observers);

        if ($this->invoice->status) {
            return view('outreachmanagement::error', ['message' => 'You can not change anything after paid the invoice!']);
        }

        return view('outreachmanagement::invoices.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateInvoiceRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateInvoiceRequest $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        try {
            $invoice->update([
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
                'seller' => $request->seller
            ]);

            //Add activity log
            $this->addLog($invoice->id, 'updated the invoice details');

            //Send the notification
            $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new InvoiceNotification($invoice, 'Outreach invoice details updated', 'updated the outreach invoice details.'));
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Invoice generated successfully!');
    }

    /**
     * Update the specified field of a row.
     * @param Request $request
     * @param int $invoice
     * @return Response
     */
    public function status(Request $request, Invoice $invoice)
    {
        //Check if the status parameter is available in the $request
        $request->validate(['status' => 'required']);

        try {
            //Update invoice status
            $status = $request->status == 'paid' ? 1 : 0;
            $invoice->update([
                'status' => ($status ?? 0),
                'payment_date' => ($status ? date('Y-m-d') : null)
            ]);

            //Add activity log
            $this->addLog($invoice->id, 'changed the invoice status to '.$request->status);

            //Send the notification
            $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new InvoiceNotification($invoice, 'Outreach invoice status updated', 'updated the outreach invoice status.'));
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Invoice status updated successfully!');
    }

    /**
     * Update the specified field of a row.
     * @param Request $request
     * @param int $invoice
     * @return Response
     */
    public function review(Request $request, Invoice $invoice)
    {
        //Check if the status parameter is available in the $request
        $request->validate(['status' => 'required']);

        try {
            //Update invoice review status
            $invoice->update([
                'review' => ($request->status ?? 0),
                'payment_date' => ($request->status ? date('Y-m-d') : null)
            ]);

            if ($request->status) {
                $message = 'proceed the invoice for payment';
                $heading = 'Outreach invoice payment request';
            } else {
                $message = 'cancelled the the payment request';
                $heading = 'Cancelled outreach invoice payment request';
            }

            //Add activity log
            $this->addLog($invoice->id, $message);

            //Send the notification
            $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new InvoiceNotification($invoice, $heading, $message));
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Invoice review status updated successfully!');
    }

    /**
     * Update the specified field of a row.
     * @param Request $request
     * @param int $invoice
     * @return Response
     */
    public function receipt(Request $request, Invoice $invoice)
    {
        //Check if the status parameter is available in the $request
        $request->validate(['file' => 'required|mimes:jpeg,jpg,png,pdf']);

        try {
            //Save the file
            if($invoice->receipt != null) {
                unlink(public_path('user-uploads/'.$invoice->receipt));
            }
            $receipt = $request->file('file')->store('outreach-management');

            //Update the invoice
            $invoice->update(['receipt' => $receipt]);

            //Add activity log
            $this->addLog($invoice->id, 'uploaded the invoice payment receipt');
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Invoice receipt uploaded successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status) {
            return Reply::error('You can not delete after paid the invoice!');
        }

        if (!in_array(auth()->id(), array_merge($this->setting->admins, $this->setting->maintainers))) {
            return Reply::error('You can not delete the invoice!');
        }

        try {
            //Change the backlinks invoice id to null
            foreach ($invoice->backlinks as $backlink) {
                $backlink->outreach_invoice_id = null;
                $backlink->save();;
            }

            //Delete the receipt file
            if($invoice->receipt != null) {
                unlink(public_path('user-uploads/'.$invoice->receipt));
            }

            //Send the notification
            $notifyTo = array_unique(array_merge($this->setting->maintainers, $this->setting->admins));
            $notifyTo = User::find($notifyTo);
            Notification::send($notifyTo, new InvoiceNotification($invoice, 'Outreach invoice deleted', 'deleted the outreach invoice.', true));

            $invoice->delete();
        } catch (Exception $e) {
            return Reply::error('Something went wrong!');
        }

        return Reply::success('Invoice deleted successfully!');
    }
}
