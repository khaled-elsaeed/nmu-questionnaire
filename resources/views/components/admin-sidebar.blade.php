<!-- Start Sidebar -->
<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('admin.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('admin.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>
    <!-- End Logobar -->

    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">
            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Questionnaires Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="questionnaires">
                    <span>Questionnaires</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.questionnaires.index') }}">View Questionnaires</a></li>
                    <li><a href="{{ route('admin.questionnaires.create') }}">Create Questionnaire</a></li>
                    <li><a href="#">View Results</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('admin/question-modules*') ? 'active' : '' }}">
    <a href="#">
        <img src="{{ asset('images/svg-icon/layouts.svg') }}" class="img-fluid" alt="modules">
        <span>Question Modules</span>
        <i class="feather icon-chevron-right pull-right"></i>
    </a>
    <ul class="vertical-submenu">
    <li class="{{ request()->routeIs('admin.question-modules.index') ? 'active' : '' }}">
            <a href="{{ route('admin.question-modules.index') }}">View Modules</a>
            @if(request()->routeIs('admin.question-modules.module'))
                @php
                    $moduleId = request()->route('id');
                    $module = \App\Models\QuestionModule::findOrFail($moduleId); // Use findOrFail for better error handling
                @endphp

                <ul class="vertical-submenu">
                    <li>
                        <a href="{{ route('admin.question-modules.module', $module->id) }}">
                            {{ $module->name }}
                        </a>
                    </li>
                </ul>
            @endif
        </li>
        <li>
            <a href="{{ route('admin.question-modules.create') }}">Create Module</a>
        </li>
        
    </ul>
</li>





            <!-- User Management Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="users">
                    <span>User Management</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="#">View Users</a></li>
                    <li><a href="#">Roles & Permissions</a></li>
                </ul>
            </li>

            <!-- Profile Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="profile">
                    <span>Profile</span>
                </a>
            </li>

            <!-- Settings Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
                    <span>Settings</span>
                </a>
            </li>

            <!-- Logout Link -->
            <li>
                <a href="#" onclick="logout()">
                    <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" id="sidebar-logout-btn" alt="logout">
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
<!-- End Sidebar -->

<script>
    function logout() {
        if (confirm("Are you sure you want to logout?")) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('logout') }}"; 
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token'; 
            csrfInput.value = '{{ csrf_token() }}'; 
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
