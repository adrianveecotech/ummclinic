<header class="c-header c-header-fixed px-3">
    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <i class="fas fa-fw fa-bars"></i>
    </button>

    <button class="c-header-toggler mfs-3 d-md-down-none" type="button" responsive="true">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>

    <ul class="c-header-nav ml-auto mr-4">
        <li class="c-header-nav-item dropdown show">
            <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
                <div class="c-avatar mr-2"><img class="c-avatar-img" src="/picture/profile_pic.jpg"></div>  
                {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-0">
                <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>
                <a class="dropdown-item @if(Auth::user()->type == "Admin") d-none @endif" href="@if(Auth::user()->type == "clinic") /clinic/profile @endif @if(Auth::user()->type == "company") /company/profile @endif">
                    <i class="fa fa-user mr-3" aria-hidden="true"></i> Profile
                </a>
                <a class="dropdown-item" href="@if(Auth::user()->type == "Admin") /admin/change-password @endif @if(Auth::user()->type == "clinic") /clinic/change-password @endif @if(Auth::user()->type == "company") /company/change-password @endif">
                    <i class="fa fa-cog mr-3" aria-hidden="true"></i> Change Password
                </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="fa fa-sign-out mr-3" aria-hidden="true"></i>Logout
            </a>
            </div>
        </li>
    </ul>
    <div class="c-subheader px-3">
        <ol class="breadcrumb border-0 m-0" style="--cui-breadcrumb-divider: '>';">
            @yield('sub_header')
        </ol>
    </div>
</header>