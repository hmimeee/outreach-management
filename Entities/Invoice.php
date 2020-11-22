<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Invoice extends Model
{
    protected $fillable = [
    	'name',
    	'amount',
    	'seller',
    	'outreach_site_id',
    	'payment_method',
    	'payment_details',
        'payment_date',
        'reviewer_id',
    	'review',
    	'status',
    	'receipt',
    ];

    protected $table = 'outreach_invoices';

    public function backlinks()
    {
    	return $this->hasMany(Backlink::class, 'outreach_invoice_id');
    }

    public function site()
    {
    	return $this->belongsTo(Site::class, 'outreach_site_id');
    }

    public function activity()
    {
        return $this->hasMany(OutreachActivity::class, 'outreach_invoice_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
