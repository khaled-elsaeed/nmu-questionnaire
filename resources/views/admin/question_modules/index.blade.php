@extends('layouts.admin')

@section('title', 'All Modules')
@section('links')
<style>
    .btn-secondary:visited {
        background-color: #234E70;
        border-color: #234E70;
    }
</style>
@endsection

@section('content')
<!-- Start col -->
<div class="col-lg-12">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title">All Modules</h5>
        </div>
        <div class="card-body">
            @if($modules->isEmpty())
                <p>No modules available.</p>
            @else
                @foreach ($modules as $module)
                    <div class="module-block mb-4 card border shadow-sm">
                        <div class="card-header bg-primary">
                            <h6 class="mb-0 text-white">{{ $module->name }}</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Description:</strong> {{ $module->description }}</p>
                            <p><small><strong>Created At:</strong> {{ $module->created_at->format('d M Y') }}</small></p>
                            <p><small><strong>Updated At:</strong> {{ $module->updated_at->format('d M Y') }}</small></p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('admin.question-modules.module', $module->id) }}" class="btn btn-secondary">
                                View Questions
                            </a>

                            <!-- Delete Module Form -->
                            <form action="{{ route('admin.question-modules.destroy', $module->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-button" data-id="{{ $module->id }}">Delete Module</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- End col -->

@section('scripts')
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.common.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const moduleId = this.getAttribute('data-id');

            // SweetAlert2 confirmation modal
            Swal.fire({
                title: 'Are you sure you want to delete this module?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete module.',
                cancelButtonText: 'No, cancel.',
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
                                form.closest('.module-block').remove(); // Remove module block after 2 seconds
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'An error occurred while deleting the module.',
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

@endsection
