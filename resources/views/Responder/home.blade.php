@extends('layouts.responder')

@section('title', 'Available Questionnaires')

@section('links')
<style>
    .card {
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row">
            @forelse($questionnaires as $target)
                <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                    <div class="card shadow-sm border-light rounded transition-all hover:shadow-lg hover:scale-105">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
                            <h5 class="card-title mb-0 font-weight-bold">{{ $target->questionnaire->title ?? 'Survey' }}</h5>
                            <i class="fa fa-question-circle"></i>
                        </div>

                        <div class="card-body p-3">
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-start"></i> 
                                <strong>Starts:</strong> {{ \Carbon\Carbon::parse($target->questionnaire->start_date)->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-hourglass-end"></i> 
                                <strong>Ends:</strong> {{ \Carbon\Carbon::parse($target->questionnaire->end_date)->format('d M, Y') }}
                            </p>
                            <p class="card-text font-weight-light">
                                <i class="fa fa-book"></i> 
                                <strong>Course:</strong> 
                                {{ $target->courseDetail->course->name ?? 'general'  }}
                            </p>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('responder.questionnaire.show', $target->id) }}" class="btn btn-primary btn-sm">
                                     Go To Questionnaire <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-footer bg-secondary text-white p-2">
                            <small><i class="fa fa-clock-o"></i> Remaining: <span id="countdown-{{$target->id}}-{{ $target->questionnaire_id }}"></span></small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No questionnaires available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        @foreach($questionnaires as $target)
            (function() {
                let endDate = new Date("{{ \Carbon\Carbon::parse($target->questionnaire->end_date)->format('Y-m-d H:i:s') }}").getTime();
                let countdownElement = document.getElementById('countdown-{{$target->id}}-{{ $target->questionnaire_id }}');

                let interval = setInterval(function() {
                    let now = new Date().getTime();
                    let timeRemaining = endDate - now;

                    let days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));

                    countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m`;

                    if (timeRemaining < 0) {
                        clearInterval(interval);
                        countdownElement.innerHTML = "EXPIRED";
                    }
                }, 1000);
            })();
        @endforeach
    </script>
@endsection

