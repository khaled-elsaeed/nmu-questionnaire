@extends('layouts.responder')

@section('title', 'Available Questionnaires')

@section('links')

@endsection

@section('content')
    <!-- Start row for available questionnaires -->
    <div class="row">
        <!-- Start col -->
        <div class="col-lg-12">
        <div class="row">
    <!-- Available Questionnaire Cards -->
    @foreach($questionnaires as $questionnaire)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card m-b-30">
                <div class="card-body">
                    <!-- Title -->
                    <h5 class="card-title">{{ $questionnaire->title }}</h5>
                    
                    <!-- Description -->
                    <p class="card-text">{{ Str::limit($questionnaire->description, 100) }}</p>
                    
                    <!-- Dates and Status -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="font-13">
                            Starts: {{ \Carbon\Carbon::parse($questionnaire->start_date)->format('d M, Y') }}
                        </span>
                        <span class="font-13">
                            Ends: {{ \Carbon\Carbon::parse($questionnaire->end_date)->format('d M, Y') }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="mt-2">
                        @if($questionnaire->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <!-- Footer with action -->
                <div class="card-footer text-center">
                    <a href="{{ route('responder.questionnaire.show', $questionnaire->id) }}" class="btn btn-primary btn-sm">
                        @if($questionnaire->is_completed ?? false)
                            View Questionnaire
                        @else
                            Start Questionnaire
                        @endif
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

        </div>
        <!-- End col -->
    </div>
    <!-- End row -->
@endsection

@section('scripts')

@endsection
