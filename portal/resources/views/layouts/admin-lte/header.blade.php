<header class="main-header">

  <!-- Logo -->
  <a href="/home" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>akvo</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>sensor</b>data</span>
  </a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
    
    <!-- Sidebar toggle button-->
    <!--a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a-->
    
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">

      <ul class="nav navbar-nav">
       {{--  @component('layouts/admin-lte/header-messages')
        @endcomponent --}}

        {{-- @component('layouts/admin-lte/header-notifications')
        @endcomponent --}}

       {{--  @component('layouts/admin-lte/header-tasks')
        @endcomponent --}}
      
        @component('layouts/admin-lte/header-user')
        @endcomponent
        
        <!-- Control Sidebar Toggle Button -->
        <!--li>
          <a href="#"><i class="fa fa-gears"></i></a>
        </li-->
      </ul>

    </div>
  </nav>
</header>