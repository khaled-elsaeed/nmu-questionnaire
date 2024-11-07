@extends('layouts.admin')

@section('title', 'Module Details')

@section('content')

<div class="col-lg-12 mb-4">
    <a href="{{ route('admin.question-modules.index') }}" class="btn btn-primary">Back to Modules</a>
</div>
<!-- Start col -->
<div class="col-lg-12">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title">Module Details: {{ $module->name }}</h5>
        </div>
        <div class="card-body">
            <h6>Description</h6>
            <p class="text-muted">{{ $module->description }}</p>

            <h6 class="mt-4">Questions</h6>
            @if($module->questions->isEmpty())
                <p class="text-muted">No questions available for this module.</p>
            @else
                @foreach ($module->questions as $question)
                    <div class="question-block mb-3 card border shadow-sm">
                        <div class="card-body">
                            <p class="font-weight-bold">Question:</p>
                            <p>{{ $question->text }}</p>
                            <p><strong>Type:</strong> <span class="badge badge-info">{{ ucfirst($question->type) }}</span></p>
                            
                            @if ($question->type === 'multiple_choice')
                                <p class="font-weight-bold">Options:</p>
                                <ul class="list-group">
                                    @foreach ($question->options as $option)
                                        <li class="list-group-item">{{ $option->text }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-3 text-right">
                                <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" style="display:inline;" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-button" data-id="{{ $question->id }}">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- End col -->

@section('scripts')
<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const questionId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this question?')) {
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
                        // Optionally, remove the question block from the DOM
                        form.closest('.question-block').remove();
                    } else {
                        alert('An error occurred while deleting the question.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
</script>
@endsection
@endsection
