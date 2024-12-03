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
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title">All Questionnaires</h5>
        </div>
        <div class="card-body">
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
                                    <p><strong>Course:</strong> {{ $questionnaire->courseDetail->course->name }}</p>
                                    <p><small><strong>Created At:</strong> {{ $questionnaire->created_at->format('d M Y') }}</small></p>
                                    <p><small><strong>Updated At:</strong> {{ $questionnaire->updated_at->format('d M Y') }}</small></p>
                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('admin.questionnaires.stats', $questionnaire->id) }}" class="btn btn-secondary">
                                        Show Stats
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // No scripts needed now as the only action is for the "Show Stats" button.
</script>
@endsection
