<!-- Start Sidebar -->
<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('responder.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('responder.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>

    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">

            <li>
                <a href="{{ route('responder.home') }}">
                    <img src="{{ asset('images/svg-icon/form_elements.svg') }}" class="img-fluid" alt="questionnaires">
                    <span>Questionnaires</span>
                </a>
            </li>

            <li>
                <a href="{{ route('responder.questionnaire.history') }}">
                    <img src="{{ asset('images/svg-icon/layouts.svg') }}" class="img-fluid" alt="results">
                    <span>History</span>
                </a>
            </li>

           
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
<!-- End Sidebar -->
