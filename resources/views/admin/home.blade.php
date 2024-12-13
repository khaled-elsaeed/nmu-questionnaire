@extends('layouts.admin')

@section('title', 'Home')


@section('content')
    <!-- Start row -->
    <div class="row">                  

        <!-- Start col for Total Questions -->
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-help-circle"></i></span>
                        </div>
                        <div class="col-7 text-end mt-2 mb-2">
                            <h5 class="card-title font-14">Total Questions</h5>
                            <h4 class="mb-0">{{ $totalQuestionnaires }}</h4>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- End col -->

        <!-- Start col for Total Modules -->
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-book"></i></span>
                        </div>
                        <div class="col-7 text-end mt-2 mb-2">
                            <h5 class="card-title font-14">Total Modules</h5>
                            <h4 class="mb-0">{{ $totalModules }}</h4>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- End col -->

        <!-- Start col for Active Questionnaires -->
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <span class="action-icon badge badge-info-inverse me-0"><i class="feather icon-check-square"></i></span>
                        </div>
                        <div class="col-7 text-end mt-2 mb-2">
                            <h5 class="card-title font-14">Active Questionnaires</h5>
                            <h4 class="mb-0">{{ $activeQuestionnaires }}</h4>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- End col -->

        <!-- Start col for Active Questionnaires -->
<div class="col-lg-3 col-md-6 mb-2">
    <div class="card m-b-30">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-5">
                    <span class="action-icon badge badge-info-inverse me-0">
                        <i class="feather icon-check-square"></i>
                    </span>
                </div>
                <div class="col-7 text-end">
                    <h5 class="card-title font-14">Responses</h5>
                    <h4 class="mb-0">{{ $totalResponses }}</h4>
                    <h5 class="card-title font-14 mt-3">Respondents</h5>
                    <h4 class="mb-0">{{ $totalStudentResponses }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- End col -->
    </div>
    <!-- End row -->
@endsection

