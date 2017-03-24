<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="/webapp/img/user.png" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{ Auth::user()->name }}</p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- search form (Optional) -->
    <!--form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
      </div>
    </form-->
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <!-- Optionally, you can add icons to the links -->
      @role('superadmin')
        <li class="header">ADMIN MENU</li>
        <li><a href="{{ route('users.index') }}">Users</a></li>
        <li><a href="{{ route('roles.index') }}">Roles</a></li>
        <li><a href="{{ route('sensors.index') }}">Sensors</a></li>
        <li><a href="{{ route('groups.index') }}">Groups</a></li>
      @endrole
      <li class="header">MENU</li>
      <li>
          <a href="/webapp/#!/dashboard"><span>Dashboard</span></a>
      </li> 
      <li>
          <a href="/webapp/#!/sensors"><span>Sensors</span></a>
      </li>
      <li>
          <a href="/webapp/#!/data"><span>Data analysis</span></a>
      </li>         
    </ul>
    <!-- /.sidebar-menu -->

  </section>
  <!-- /.sidebar -->
</aside>