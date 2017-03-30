<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="/uploads/avatars/{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{ Auth::user()->name }}</p>
        <!-- Status -->
        @role('superadmin')<p><i class="fa fa-user-secret"></i> Super admin</p>   @endrole
        @role('admin')     <p><i class="fa fa-user-md"></i> Administrator</p>     @endrole
        @role('manager')   <p><i class="fa fa-user"></i> Sensor manager</p>       @endrole
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
        <li class="header">SUPER ADMIN MENU</li>
        <li><a href="{{ route('roles.index') }}"><i class="fa fa-address-book-o"></i><span>Roles</span></a></li>
        <li><a href="{{ route('users.index') }}"><i class="fa fa-user-circle-o"></i><span>Users</span></a></li>
        <li><a href="{{ route('sensors.index') }}"><i class="fa fa-cube "></i><span>Sensors</span></a></li>
        <li><a href="{{ route('groups.index') }}"><i class="fa fa-cubes"></i><span>Groups</span></a></li>
      @endrole
      @role('admin')
        <li class="header">ADMIN MENU</li>
        <li><a href="{{ route('users.index') }}"><i class="fa fa-user-circle-o"></i><span>Users</span></a></li>
        <li><a href="{{ route('sensors.index') }}"><i class="fa fa-cube "></i><span>Sensors</span></a></li>
        <li><a href="{{ route('groups.index') }}"><i class="fa fa-cubes"></i><span>Groups</span></a></li>
      @endrole
      @role('manager')
        <li class="header">MANAGER MENU</li>
        <li><a href="{{ route('sensors.index') }}"><i class="fa fa-cube "></i><span>Sensors</span></a></li>
        <li><a href="{{ route('groups.index') }}"><i class="fa fa-cubes"></i><span>Groups</span></a></li>
      @endrole
      <li class="header">APP MENU</li>
      <li>
          <a href="/webapp/#!/dashboard"><i class="fa fa-dashboard"></i><span>Dashboard</span></a>
      </li> 
      <li>
          <a href="/webapp/#!/sensors"><i class="fa fa-dot-circle-o"></i><span>Sensor data</span></a>
      </li>
      <li>
          <a href="/webapp/#!/data"><i class="fa fa-bar-chart"></i><span>Data analysis</span></a>
      </li>         
    </ul>
    <!-- /.sidebar-menu -->

  </section>
  <!-- /.sidebar -->
</aside>