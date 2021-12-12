@php
$sett = \Modules\OutreachManagement\Entities\OutreachSetting::first();
$authUsers = array_merge($sett->admins, $sett->observers, $sett->maintainers);
@endphp
@if(in_array(auth()->id(), $authUsers))

<li>
    <a href="javascript:;" class="waves-effect">
        <i class="ti-share-alt"></i>
        <span class="hide-menu"> Outreach Management <span class="fa arrow"></span></span>
    </a>
    <ul class="nav nav-second-level collapse">
        <li>
            <a href="{{route('member.outreach-management.index')}}" class="waves-effect">
                <span class="hide-menu">Sites</span>
            </a>
        </li>

        <li>
            <a href="{{route('member.outreach-backlinks.index')}}" class="waves-effect">
                <span class="hide-menu">Backlinks</span>
            </a>
        </li>

        <li>
            <a href="{{route('member.outreach-invoices.index')}}" class="waves-effect">
                <span class="hide-menu">Invoices</span>
            </a>
        </li>
    </ul>
</li>

@endif