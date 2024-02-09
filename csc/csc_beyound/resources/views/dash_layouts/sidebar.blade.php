<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">      
              @if (auth()->user()->role == 'teacher')
            Teacher
        @elseif (auth()->user()->role == 'student')
            
       Student
            
        @endif </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">




    <!-- Heading -->
    <div class="sidebar-heading">
        Students
    </div>


        @if (auth()->user()->role == 'teacher')

    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.students.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Students</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.subjects.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Subjects</span></a>
    </li>

    @endif
    @if (auth()->user()->role == 'student')

    <li class="nav-item">
        <a class="nav-link" href="{{route('student.dashboard')}}">
            <i class="fas fa-fw fa-table"></i>
            <span>My Subjects  </span></a>
    </li>
    @endif
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" style="display: none;" id="logout-form">
            @csrf
        </form>
        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-fw fa-table"></i>
            <span>Log Out</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

  

</ul>