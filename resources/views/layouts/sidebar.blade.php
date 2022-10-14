<!-- Main Sidebar Container -->
<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
    <img src="{{ asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8;background: #fff;">
        <span class="brand-text font-weight-light">Printstop India</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image" >
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    Hello Printstopions!
                </a>
            </div>            
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <?php $urls = url()->current(); ?>
                <li class="nav-item has-treeview menu-open">
                    <a href="{{ url('clickpost') }}" class="nav-link @if(str_contains(url()->current(), url('clickpost'))) active @endif">
                        <i class="nav-icon fas fa-product-hunt"></i>
                        <p>Clickpost</p>
                    </a>
                </li>
                <li class="nav-item has-treeview menu-open">
                    <a href="{{ url('clickpost/recommendations') }}" class="nav-link @if(str_contains(url()->current(), url('clickpost/recommendations'))) active @endif">
                        <i class="nav-icon fas fa-product-hunt"></i>
                        <p>Recommendation</p>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
    <div class="sidebar-custom">
      <a href="#" class="btn btn-link"><i class="fas fa-cogs"></i></a>
      <a href="#" class="btn btn-secondary hide-on-collapse pos-right">Help</a>
    </div>
    
</aside>