<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
    	'website',
    	'niche',
    	'domain_rating',
        'spam_score',
    	'traffic',
    	'post_price',
    	'link_price',
        'ahref_link',
    	'ahref_snap',
    	'traffic_value',
    	'notes',
    	'status',
    ];

    protected $table = 'outreach_sites';

    public function getSiteAttribute()
    {
        return str_replace('www.', '', parse_url($this->website)['host']);
    }

    public function activity()
    {
        return $this->hasMany(OutreachActivity::class, 'outreach_site_id');
    }

    public function backlinks()
    {
        return $this->hasMany(Backlink::class, 'outreach_site_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'outreach_site_id');
    }

    public function comments()
    {
        return $this->hasMany(OutreachComment::class, 'outreach_site_id');
    }
}
