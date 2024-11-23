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
                @foreach ($questionnaires as $questionnaire)
                    <div class="questionnaire-block mb-4 card border shadow-sm">
                        <div class="card-header bg-primary">
                            <h6 class="mb-0 text-white">{{ $questionnaire->title }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Description:</strong> {{ $questionnaire->description }}</p>
                            <p><small><strong>Created At:</strong> {{ $questionnaire->created_at->format('d M Y') }}</small></p>
                            <p><small><strong>Updated At:</strong> {{ $questionnaire->updated_at->format('d M Y') }}</small></p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="#" class="btn btn-secondary">
                                View Questions
                            </a>

                            <!-- Delete Module Form -->
                            <form action="#" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-button" data-id="{{ $questionnaire->id }}">Delete Module</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
                        form.closest('.questionnaire-block').remove();
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
