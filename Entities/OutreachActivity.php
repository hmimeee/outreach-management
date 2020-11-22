<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OutreachActivity extends Model
{
    protected $fillable = [
        'outreach_site_id',
    	'outreach_backlink_id',
        'outreach_invoice_id',
    	'user_id',
    	'type',
    	'message',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
