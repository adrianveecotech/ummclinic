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
            <a href="/" class="c-sidebar-nav-link">
                <i class="fa fa-home fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Dashboard</p>
            </a>
        </li>

        <li class="c-sidebar-nav-item">
            <a href="/company/employee" class="c-sidebar-nav-link">
                <i class="fa fa-user fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Employee</p>
            </a>
        </li> 
        <li class="c-sidebar-nav-item">
            <a href="/company/dependent" class="c-sidebar-nav-link">
                <i class="fa fa-user fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Dependent</p>
            </a>
        </li>        
        <li class="c-sidebar-nav-item">
            <a href="/company/consultation" class="c-sidebar-nav-link">
                <i class="fa fa-user-md fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Consultation</p>
            </a>
        </li>        
        <li class="c-sidebar-nav-item">
            <a href="/company/payment" class="c-sidebar-nav-link">
                <i class="fa fa-money fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Payment</p>
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a href="/company/invoice" class="c-sidebar-nav-link">
                <i class="fa fa-file fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Invoice</p>
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a href="/company/mc" class="c-sidebar-nav-link">
                <i class="fa fa-file fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>MC</p>
            </a>
        </li>
    </ul>

</div>