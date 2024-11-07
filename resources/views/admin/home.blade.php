
@extends('layouts.admin')

@section('title', 'Home')

@section('links')
    <!-- Page-specific CSS -->
        <!-- Apex css -->
<link href="{{ asset('plugins/apexcharts/apexcharts.css') }}" rel="stylesheet">
<!-- Slick css -->
<link href="{{ asset('plugins/slick/slick.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/slick/slick-theme.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Start row -->
    <div class="row">                  
                    <!-- Start col -->
                    <div class="col-lg-12">
                        <!-- Start row -->
                        <div class="row">
                            <!-- Start col -->
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                                            </div>
                                            <div class="col-7 text-end mt-2 mb-2">
                                                <h5 class="card-title font-14">Students</h5>
                                                <h4 class="mb-0">2585</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <span class="font-13">Updated Today</span>
                                            </div>
                                            <div class="col-4 text-end">
                                                <span class="badge badge-success"><i class="feather icon-trending-up me-1"></i>25%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End col -->
                            <!-- Start col -->
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-award"></i></span>
                                            </div>
                                            <div class="col-7 text-end mt-2 mb-2">
                                                <h5 class="card-title font-14">Males</h5>
                                                <h4 class="mb-0">263</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <p class="font-13">Updated 1 Day ago</p>
                                            </div>
                                            <div class="col-4 text-end">
                                                <span class="badge badge-warning"><i class="feather icon-trending-down me-1"></i>23%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End col -->
                            <!-- Start col -->
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-briefcase"></i></span>
                                            </div>
                                            <div class="col-7 text-end mt-2 mb-2">
                                                <h5 class="card-title font-14">Females</h5>
                                                <h4 class="mb-0">45</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <p class="font-13">Updated 3 Day ago</p>
                                            </div>
                                            <div class="col-4 text-end">
                                                <span class="badge badge-success"><i class="feather icon-trending-up me-1"></i>15%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End col -->
                            <!-- Start col -->
                            <div class="col-lg-3 col-md-6 mb-2">
                                <div class="card m-b-30">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-book-open"></i></span>
                                            </div>
                                            <div class="col-7 text-end mt-2 mb-2">
                                                <h5 class="card-title font-14">Occupancy</h5>
                                                <h4 class="mb-0">93</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <p class="font-13">Updated 5 Day ago</p>
                                            </div>
                                            <div class="col-4 text-end">
                                                <span class="badge badge-warning"><i class="feather icon-trending-down me-1"></i>10%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End col -->
                        </div>
                        <!-- End row -->
                    </div>
                    <!-- End col -->
                </div>
                <!-- End row -->
                <!-- Start row -->
                <div class="row">                  
                    <!-- Start col -->
                    <div class="col-lg-12 col-xl-9">
                        <!-- Start row -->
                        <div class="row">
                           
                            <!-- Start col -->
                            <div class="col-lg-12 col-xl-9">
                                <div class="card m-b-30">
                                    <div class="card-header">                                
                                        <h5 class="card-title mb-0">Course</h5>
                                    </div>
                                    <div class="card-body text-center z-0">
                                        <div class="course-slider">
                                            <div class="course-slider-item">
                                                <h4 class="my-0">Mathematics</h4>
                                                <div class="row align-items-center my-4 py-3">
                                                    <div class="col-4 p-0">
                                                        <h4>24</h4>
                                                        <p class="mb-0">Faculty</p>
                                                    </div>
                                                    <div class="col-4 py-3 px-0 bg-primary-rgba rounded">
                                                        <h4 class="text-primary">543</h4>
                                                        <p class="text-primary mb-0">Students</p>
                                                    </div>
                                                    <div class="col-4 p-0">
                                                        <h4>09</h4>
                                                        <p class="mb-0">Class</p>
                                                    </div>
                                                </div>
                                                <div class="progress mb-2 mt-2" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-6 text-start">
                                                        <p class="font-13">80% Completed</p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <p class="font-13">19/25 Module</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="course-slider-item">
                                                <h4 class="my-0">Science</h4>
                                                <div class="row align-items-center my-4 py-3">
                                                    <div class="col-4 p-0">
                                                        <h4>22</h4>
                                                        <p class="mb-0">Faculty</p>
                                                    </div>
                                                    <div class="col-4 py-3 px-0 bg-success-rgba rounded">
                                                        <h4 class="text-success">350</h4>
                                                        <p class="text-success mb-0">Students</p>
                                                    </div>
                                                    <div class="col-4 p-0">
                                                        <h4>05</h4>
                                                        <p class="mb-0">Class</p>
                                                    </div>
                                                </div>
                                                <div class="progress mb-2" style="height: 5px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-6 text-start">
                                                        <span class="font-13">70% Completed</span>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <span class="font-13">17/25 Module</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="course-slider-item">
                                                <h4 class="my-0">English</h4>
                                                <div class="row align-items-center my-4 py-3">
                                                    <div class="col-4 p-0">
                                                        <h4>18</h4>
                                                        <p class="mb-0">Faculty</p>
                                                    </div>
                                                    <div class="col-4 py-3 px-0 bg-secondary-rgba rounded">
                                                        <h4 class="text-secondary">470</h4>
                                                        <p class="text-secondary mb-0">Students</p>
                                                    </div>
                                                    <div class="col-4 p-0">
                                                        <h4>15</h4>
                                                        <p class="mb-0">Class</p>
                                                    </div>
                                                </div>
                                                <div class="progress mb-2" style="height: 5px;">
                                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-6 text-start">
                                                        <span class="font-13">60% Completed</span>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <span class="font-13">15/25 Module</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            <!-- End col -->
                        </div>
                        <!-- End row -->
                    </div>
                    <!-- End col -->
                </div>
                <!-- End row -->
@endsection

@section('scripts')
    <!-- Page-specific JS -->
    <!-- Apex js -->
<script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('plugins/apexcharts/irregular-data-series.js') }}"></script>

<!-- Slick js -->
<script src="{{ asset('plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('js/custom/custom-dashboard-school.js') }}"></script>
@endsection
