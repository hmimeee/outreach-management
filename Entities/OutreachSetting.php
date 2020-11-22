<?php

namespace Modules\OutreachManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OutreachSetting extends Model
{
    protected $fillable = [
    	'admins',
    	'maintainers',
    	'observers',
    ];

    protected $casts = [
        'admins' => 'array',
        'maintainers' => 'array',
        'observers' => 'array',
    ];

    public static function getAdmins()
    {
     $admins = OutreachSetting::pluck('admins');
     return User::whereIn('id', [$admins])->get();
 }

 public static function getMaintainers()
 {
     $maintainers = OutreachSetting::pluck('maintainers');
     return User::whereIn('id', [$maintainers])->get();
 }

 public static function getObservers()
 {
     $observers = OutreachSetting::pluck('observers');
     return User::whereIn('id', [$observers])->get();
 }
}
