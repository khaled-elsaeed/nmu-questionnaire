@extends('layouts.responder')

@section('title', 'Past Questionnaires')

@section('content')
    <div class="container mt-4">
        <div class="row">
            {{-- Answered Questionnaires --}}
            @foreach($questionnaireData['answered'] as $item)
                <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                    <div class="card shadow-sm border-light rounded transition-all hover:shadow-lg hover:scale-105">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center p-3">
                            <h5 class="card-title mb-0 font-weight-bold">{{ $item['questionnaire']->title }}</h5>
                            <span class="badge bg-white text-success">Completed</span>
                        </div>

                        <div class="card-body p-3">
                            <p class="card-text font-weight-light">
                                <i class="fa fa-calendar-check"></i> 
                                <strong>Completed on:</strong> {{ $item['response_date']->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-start"></i> 
                                <strong>Started:</strong> {{ \Carbon\Carbon::parse($item['questionnaire']->start_date)->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-end"></i> 
                                <strong>Ended:</strong> {{ \Carbon\Carbon::parse($item['questionnaire']->end_date)->format('d M, Y') }}
                            </p>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('responder.questionnaire.show', $item['questionnaire']->id) }}" class="btn btn-success btn-sm">
                                    View Answers <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-footer bg-light text-muted p-2">
                            <small><i class="fa fa-clock-o"></i> Created {{ \Carbon\Carbon::parse($item['questionnaire']->created_at)->format('d M, Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Available/Deadline Passed Questionnaires --}}
            @foreach($questionnaireData['available'] as $item)
                <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                    <div class="card shadow-sm border-light rounded transition-all hover:shadow-lg hover:scale-105">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center p-3">
                            <h5 class="card-title mb-0 font-weight-bold">{{ $item['questionnaire']->title }}</h5>
                            @if(\Carbon\Carbon::parse($item['questionnaire']->end_date)->isPast())
                                <span class="badge bg-danger text-white">Deadline Passed</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </div>

                        <div class="card-body p-3">
                            <p class="card-text font-weight-light">
                                <i class="fa fa-calendar-times"></i> 
                                <strong>Status:</strong> 
                                @if(\Carbon\Carbon::parse($item['questionnaire']->end_date)->isPast())
                                    <span class="text-danger">Deadline Passed</span>
                                @else
                                    <span class="text-warning">Not Completed</span>
                                @endif
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-start"></i> 
                                <strong>Starts:</strong> {{ \Carbon\Carbon::parse($item['questionnaire']->start_date)->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-end"></i> 
                                <strong>Ends:</strong> {{ \Carbon\Carbon::parse($item['questionnaire']->end_date)->format('d M, Y') }}
                            </p>

                            <div class="d-flex justify-content-end">
                                @if(!\Carbon\Carbon::parse($item['questionnaire']->end_date)->isPast())
                                    <a href="{{ route('responder.questionnaire.show', $item['questionnaire']->id) }}" class="btn btn-warning btn-sm">
                                        Take Survey <i class="fa fa-arrow-right"></i>
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        Expired <i class="fa fa-lock"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer bg-light text-muted p-2">
                            <small><i class="fa fa-clock-o"></i> Created {{ \Carbon\Carbon::parse($item['questionnaire']->created_at)->format('d M, Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($questionnaireData['answered']) === 0 && count($questionnaireData['available']) === 0)
            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle"></i> No questionnaires found.
            </div>
        @endif
    </div>
@endsection