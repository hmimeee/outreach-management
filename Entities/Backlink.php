<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Project;

class Backlink extends Model
{
    protected $fillable = [
    	'project_id',
    	'outreach_site_id',
        'website',
    	'backlink',
        'url',
    	'type',
    	'published_date',
    	'indexed',
    	'paid',
        'cost',
    	'status',
    	'remarks',
        'outreach_invoice_id',
    ];

    protected $table = 'outreach_backlinks';

    public function site()
    {
    	return $this->belongsTo(Site::class, 'outreach_site_id');
    }

    public function project()
    {
    	return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'outreach_invoice_id');
    }

    public function activity()
    {
        return $this->hasMany(OutreachActivity::class, 'outreach_backlink_id');
    }

    public function comments()
    {
        return $this->hasMany(OutreachComment::class, 'outreach_backlink_id');
    }
}
