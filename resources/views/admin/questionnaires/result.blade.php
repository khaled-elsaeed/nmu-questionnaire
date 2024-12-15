@extends('layouts.admin')

@section('title', 'All Questionnaires Results')

@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
<style>
   .loading {
      pointer-events: none; /* Disable button interactions */
   }
</style>
@endsection

@section('content')
<!-- End row -->
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <!-- Title on the Left -->
         <h2 class="page-title text-primary mb-2 mb-md-0">Questionnaire Results</h2>
         <!-- Toggle Button on the Right -->
         <div class="div">
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
               <i class="fa fa-search-plus"></i>
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Search Filter -->
<div class="collapse" id="collapseExample">
   <div class="search-filter-container card card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
         <!-- Search Box with Icon on the Left -->
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control search-input" id="searchBox" placeholder="Search..." />
         </div>
      </div>
   </div>
</div>

<div class="row">
    <!-- Start col -->
    <div class="col-lg-12">
        <div class="card m-b-30 table-card">
            <div class="card-body table-container">
                <div class="table-responsive">
                    <table id="default-datatable" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Faculty</th>
                                <th>Program</th>
                                <th>Responses</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questionnaires as $index => $questionnaire)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $questionnaire->questionnaire->title }}</td>
                                <td>
                                    @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course))
                                        {{ $questionnaire->courseDetail->course->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
    @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course->faculty))
        {{ $questionnaire->courseDetail->course->faculty->name }}
    @else
        Multiple Faculties
    @endif
</td>

<td>
    @if(isset($questionnaire->courseDetail) && isset($questionnaire->courseDetail->course->program))
        {{ $questionnaire->courseDetail->course->program->name }}
    @else
        Multiple Programs
    @endif
</td>

                                <td>
    @if($questionnaire->response_count > 0)
        {{ $questionnaire->response_count }}
    @else
        N/A
    @endif
</td>


                                <td>{{ $questionnaire->created_at->format('d M Y') }}</td>
                                <td>{{ $questionnaire->updated_at->format('d M Y') }}</td>
                                <td>
                                    <!-- Replaced the link with a button -->
                                    <button onclick="window.location.href='{{ route('admin.questionnaires.stats', $questionnaire->id) }}'" class="btn btn-secondary btn-sm rounded-pill">
    <i class="fa fa-eye"></i>
</button>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->
</div>

@endsection

@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#default-datatable').DataTable({
            "pageLength": 10,
            "order": [[0, "asc"]]
        });

        // Bind search input to DataTable's search function
        $('#searchBox').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endsection
