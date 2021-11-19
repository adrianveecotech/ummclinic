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
            <a href="/admin" class="c-sidebar-nav-link">
                <i class="fa fa-home fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Dashboard</p>
            </a>
        </li>        

        <li class="c-sidebar-nav-title">User Management</li>
        <li class="c-sidebar-nav-item">
            <a href="/admin/clinic" class="c-sidebar-nav-link">
                <i class="fa fa-hospital-o fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Clinic</p>
            </a>
        </li>  
        <li class="c-sidebar-nav-item">
            <a href="/admin/company" class="c-sidebar-nav-link">
                <i class="fa fa-industry fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Company</p>
            </a>
        </li>        
        
        <li class="c-sidebar-nav-title">Patient Management</li>
        <li class="c-sidebar-nav-item">
            <a href="/admin/employee" class="c-sidebar-nav-link">
                <i class="fa fa-user fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Employee</p>
            </a>
        </li>         
        <li class="c-sidebar-nav-item">
            <a href="/admin/consultation" class="c-sidebar-nav-link">
                <i class="fa fa-user-md fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Consultation History</p>
            </a>
        </li>          
        <li class="c-sidebar-nav-item">
            <a href="/admin/payment-history" class="c-sidebar-nav-link">
                <i class="fa fa-credit-card fa-2x"></i>&nbsp;&nbsp;&nbsp;
                <p>Payment History</p>
            </a>
        </li>  
    </ul>

</div>