@extends('layouts.admin')

@section('title', 'Module Details')
@section('links')
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')

<!-- Back to Modules Button -->
<div class="col-lg-12 mb-4">
    <a href="{{ route('admin.question-modules.index') }}" class="btn btn-primary btn-sm rounded-pill">
        <i class="fa fa-arrow-left mr-2"></i> Back to Modules
    </a>
</div>

<!-- Module Details -->
<div class="col-lg-12">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Module Details: {{ $module->name }}</h5>
        </div>
        <div class="card-body">
            <h6 class="font-weight-bold">Description</h6>
            <p class="text-muted">{{ $module->description }}</p>

            <!-- Questions Section -->
            <h6 class="mt-4 font-weight-bold">Questions</h6>
            @if($module->questions->isEmpty())
                <p class="text-muted">No questions available for this module.</p>
            @else
                <div class="list-group">
                    @foreach ($module->questions as $index => $question)
                        <div class="list-group-item card border shadow-sm mb-3">
                            <div class="card-body border border-primary">
                                <!-- Question Index -->
                                <h6 class="font-weight-bold">Q{{ $index + 1 }}</h6>
                                <h5 class="font-weight-bold">{{ $question->text }}</h6>

                                <!-- Question Type -->
                                <p><strong>Type:</strong> <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $question->type)) }}</span></p>

                                <!-- Options for Multiple Choice -->
                                @if ($question->type === 'multiple_choice')
                                    <p class="font-weight-bold">Options:</p>
                                    <ul class="list-group">
                                        @foreach ($question->options as $option)
                                            <li class="list-group-item">{{ $option->text }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <!-- Delete Button -->
                                <div class="mt-3 text-right">
                                    <button type="button" class="btn btn-danger btn-sm rounded-pill delete-button" data-id="{{ $question->id }}">
                                        <i class="fa fa-trash-alt"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts for Deletion with SweetAlert2 -->
@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const questionId = this.getAttribute('data-id');

            swal({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                    fetch('{{ route('admin.questions.destroy', '') }}/' + questionId, {
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
                            swal(
                                'Deleted!',
                                data.message,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            swal(
                                'Error!',
                                'An error occurred while deleting the question.',
                                'error'
                            );
                        }

                    })
                    .catch(error => console.error('Error:', error));
                
            });
        });
    });
</script>
@endsection

@endsection
