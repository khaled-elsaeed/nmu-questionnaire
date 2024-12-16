<!-- Start Sidebar -->
<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
    @if(auth()->user()->hasRole('super_admin'))
    <a href="{{ route('admin.home') }}" class="logo logo-large">
        <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
    </a>
    <a href="{{ route('admin.home') }}" class="logo logo-small">
        <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
    </a>
@elseif(auth()->user()->hasRole('admin'))
    <a href="javascript:void(0)" class="logo logo-large">
        <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
    </a>
    <a href="javascript:void(0)" class="logo logo-small">
        <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
    </a>
@endif

    </div>
    <!-- End Logobar -->

    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">
            <!-- Dashboard Link -->
            @if(auth()->user()->hasRole('super_admin'))
            <li>
                <a href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Dashboard</span>
                </a>
            </li>
            @endif

            <!-- Questionnaires Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="questionnaires">
                    <span>Questionnaires</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    @if(auth()->user()->hasRole('super_admin'))
                        <!-- Super Admin: Show All Links -->
                        <li><a href="{{ route('admin.questionnaires.index') }}">View Questionnaires</a></li>
                        <li><a href="{{ route('admin.questionnaires.create') }}">Create Questionnaire</a></li>
                        <li><a href="{{ route('admin.questionnaires.results') }}">View Results</a></li>
                    @elseif(auth()->user()->hasRole('admin'))
                        <!-- Admin: Only Show Results Link -->
                        <li><a href="{{ route('admin.questionnaires.results') }}">View Results</a></li>
                    @endif
                </ul>
            </li>
            @if(auth()->user()->hasRole('super_admin'))

            <!-- Question Modules Link -->
            <li class="{{ request()->is('admin/question-modules*') ? 'active' : '' }}">
                <a href="#">
                    <img src="{{ asset('images/svg-icon/layouts.svg') }}" class="img-fluid" alt="modules">
                    <span>Question Modules</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                        <!-- Super Admin: Show All Module Links -->
                        <li class="{{ request()->routeIs('admin.question-modules.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.question-modules.index') }}">View Modules</a>
                        </li>
                        <li><a href="{{ route('admin.question-modules.create') }}">Create Module</a></li>
                   
                </ul>
            </li>
            @endif

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
