@extends('layouts.admin')

@section('title', 'All Questionnaires')

@section('links')
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="col-lg-12">
    <h5 class="card-title mb-4">All Questionnaires</h5>
    @if($questionnaires->isEmpty())
        <p>No questionnaires available.</p>
    @else
        <div class="d-flex flex-wrap justify-content-start">
            @foreach ($questionnaires as $questionnaire)
                <div class="card border shadow-sm mx-2 mb-4" style="max-width: 350px;">
                    <div class="card-header bg-primary">
                        <!-- Question Icon + Questionnaire Title -->
                        <h6 class="mb-0 text-white">
                            <i class="fa fa-question-circle mr-2"></i> <!-- Question Icon -->
                            {{ $questionnaire->title }} <!-- Use title instead of name -->
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Description:</strong> {{ $questionnaire->description }}</p>
                        <p><small><strong>Created At:</strong> {{ $questionnaire->created_at->format('d M Y') }}</small></p>
                        <p><small><strong>Updated At:</strong> {{ $questionnaire->updated_at->format('d M Y') }}</small></p>
                    </div>
                    <div class="card-footer text-right">
                        <a href="#" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fa fa-eye"></i> View Questions
                        </a>
                        <!-- Delete Module Form -->
                        <form action="#" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-button" data-id="{{ $questionnaire->id }}">
                                <i class="fa fa-trash-alt"></i> Delete Module
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.common.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const questionnaireId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this questionnaire? This action cannot be undone.')) {
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        form.closest('.card').remove(); // Remove the questionnaire card
                    } else {
                        alert('An error occurred while deleting the questionnaire.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
</script>
@endsection
