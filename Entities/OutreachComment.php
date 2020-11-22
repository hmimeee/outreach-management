<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OutreachComment extends Model
{
    protected $fillable = [
    	'outreach_site_id',
    	'outreach_backlink_id',
        'outreach_invoice_id',
    	'user_id',
    	'message',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
