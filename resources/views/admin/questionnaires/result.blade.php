@extends('layouts.admin')

@section('title', 'All Questionnaires')

@section('links')
<style>
    .btn-secondary:visited {
        background-color: #234E70;
        border-color: #234E70;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12">
    <h5 class="card-title mb-4">All Questionnaires</h5>
    @if($questionnaires->isEmpty())
        <p>No questionnaires available.</p>
    @else
        <div class="row">
            @foreach ($questionnaires as $questionnaire)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 text-white">{{ $questionnaire->questionnaire->title }}</h6>
                        </div>
                        <div class="card-body">
                            <!-- Course -->
                            @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course))
                                <p><strong>Course:</strong> {{ $questionnaire->courseDetail->course->name }}</p>
                            @endif

                            <!-- Faculty -->
                            @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course->faculty))
                                <p><strong>Faculty:</strong> {{ $questionnaire->courseDetail->course->faculty->name }}</p>
                            @endif

                            <!-- Department -->
                            @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course->department))
                                <p><strong>Department:</strong> {{ $questionnaire->courseDetail->course->department->name }}</p>
                            @endif

                            <!-- Program -->
                            @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course->program))
                                <p><strong>Program:</strong> {{ $questionnaire->courseDetail->course->program->name }}</p>
                            @endif

                            <p><small><strong>Created At:</strong> {{ $questionnaire->created_at->format('d M Y') }}</small></p>
                            <p><small><strong>Updated At:</strong> {{ $questionnaire->updated_at->format('d M Y') }}</small></p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('admin.questionnaires.stats', $questionnaire->id) }}" class="btn btn-secondary btn-sm rounded-pill">
                                Show Statistics
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection