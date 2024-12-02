@extends('layouts.responder')

@section('title', 'Past Questionnaires')

@section('content')
    <div class="container mt-4">
        <div class="row">
            {{-- Expired or Completed Questionnaires --}}
            @forelse($availableQuestionnaires as $target)
                <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                    <div class="card shadow-sm border-light rounded transition-all hover:shadow-lg hover:scale-105">
                        <div class="card-header 
                            @if($target->response_exists) 
                                bg-success text-white
                            @else
                                bg-secondary text-white
                            @endif
                            d-flex justify-content-between align-items-center p-3">
                            
                            <h5 class="card-title mb-0 font-weight-bold">{{ $target->questionnaire_title }}</h5>
                            
                            <span class="badge 
                                @if($target->response_exists) 
                                    bg-white text-success
                                @else
                                    bg-white text-secondary
                                @endif
                                ">
                                @if($target->response_exists) 
                                    Completed
                                @else 
                                    Expired
                                @endif
                            </span>
                        </div>

                        <div class="card-body p-3">
                            <p class="card-text font-weight-light">
                                <i class="fa fa-calendar-check"></i> 
                                <strong>Start Date:</strong> {{ \Carbon\Carbon::parse($target->questionnaire_start_date)->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-end"></i> 
                                <strong>End Date:</strong> {{ \Carbon\Carbon::parse($target->questionnaire_end_date)->format('d M, Y') }}
                            </p>

                            <p class="card-text font-weight-light">
                                <i class="fa fa-book"></i> 
                                <strong>Course:</strong> 
                                {{ $target->course_name}}
                            </p>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('responder.questionnaire.show', $target->id) }}" class="btn 
                                    @if($target->response_exists)
                                        btn-success
                                    @else
                                        btn-secondary
                                    @endif
                                    btn-sm">
                                    @if($target->response_exists) 
                                        View Responses <i class="fa fa-arrow-right"></i>
                                    @else 
                                        View Expired <i class="fa fa-arrow-right"></i>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <div class="card-footer bg-light text-muted p-2">
                            <small><i class="fa fa-clock-o"></i> Created on {{ \Carbon\Carbon::parse($target->created_at)->format('d M, Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center w-100">
                    <i class="fa fa-info-circle"></i> No expired or completed questionnaires found.
                </div>
            @endforelse
        </div>
    </div>
@endsection
