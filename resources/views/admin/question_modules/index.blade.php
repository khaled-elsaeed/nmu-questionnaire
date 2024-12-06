@extends('layouts.admin')

@section('title', 'All Modules')
@section('links')
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<!-- Start col -->
<div class="col-lg-12">
    <h5 class="card-title mb-4">All Modules</h5>
    @if($modules->isEmpty())
        <p>No modules available.</p>
    @else
        <div class="d-flex flex-wrap justify-content-start">
            @foreach ($modules as $module)
                <div class="card border shadow-sm mx-2 mb-4" style="max-width: 350px;">
                    <div class="card-header bg-primary">
                        <!-- Question Icon + Module Title -->
                        <h6 class="mb-0 text-white">
                            <i class="fa fa-question-circle mr-2"></i> <!-- Question Icon -->
                            {{ $module->name }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Description:</strong> {{ $module->description }}</p>
                        <p><small><strong>Created At:</strong> {{ $module->created_at->format('d M Y') }}</small></p>
                        <p><small><strong>Updated At:</strong> {{ $module->updated_at->format('d M Y') }}</small></p>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.question-modules.module', $module->id) }}" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fa fa-eye"></i> View Questions
                        </a>
                        <!-- Delete Module Form -->
                        <form action="{{ route('admin.question-modules.destroy', $module->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-button" data-id="{{ $module->id }}">
                                <i class="fa fa-trash-alt"></i> Delete Module
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<!-- End col -->

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
   document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const moduleId = this.getAttribute('data-id');

            // SweetAlert2 Confirmation
           swal({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                    // Perform the delete action if confirmed
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
                           swal(
                                'Deleted!',
                                data.message,
                                'success'
                            );
                            window.location.reload();
                        } else {
                           swal(
                                'Error!',
                                'An error occurred while deleting the module.',
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
