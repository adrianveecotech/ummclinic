<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

<style>
    a:hover{
        text-decoration: none;
    }
</style>

<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="/">
            {{ config('app.name'); }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="/clinic" class="c-sidebar-nav-link">
                <i class="fa fa-home fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Dashboard</p>
            </a>
        </li>

        <li class="c-sidebar-nav-title">Clinic Management</li>
        <li class="c-sidebar-nav-item">
            <a href="/clinic/staff" class="c-sidebar-nav-link">
                <i class="fa fa-user fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Staff</p>
            </a>
        </li>        
        <li class="c-sidebar-nav-item">
            <a href="/clinic/payment" class="c-sidebar-nav-link">
                <i class="fa fa-credit-card fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Payment</p>
            </a>
        </li>

        <li class="c-sidebar-nav-title">Patient Management</li>
        <li class="c-sidebar-nav-item">
            <a href="/clinic/search" class="c-sidebar-nav-link">
                <i class="fa fa-search fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Search Patient</p>
            </a>
        </li>        
        <li class="c-sidebar-nav-item">
            <a href="/clinic/consultation" class="c-sidebar-nav-link">
                <i class="fa fa-user-md fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Consultation History</p>
            </a>
        </li>
    </ul>

</div>