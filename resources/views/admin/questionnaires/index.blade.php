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

                            <!-- Delete Questionnaire Form -->
                            <form action="{{ route('admin.questionnaires.destroy', $questionnaire->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-button" data-id="{{ $questionnaire->id }}">Delete Questionnaire</button>
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

            // SweetAlert2 confirmation modal
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    // Proceed with deletion
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
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                form.closest('.questionnaire-block').remove(); // Remove questionnaire block after 2 seconds
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'An error occurred while deleting the questionnaire.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
</script>
@endsection
