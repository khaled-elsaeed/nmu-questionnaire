@extends('layouts.admin')

@section('content')
<!-- Back to Modules Button -->
<div class="col-lg-12 mb-4">
    <a href="{{ route('admin.questionnaires.results') }}" class="btn btn-primary btn-sm rounded-pill">
        <i class="fa fa-arrow-left me-2"></i> العودة إلى النتائج
    </a>
</div>

<!-- Main Content Section -->
<div class="container mt-5" style="direction: rtl; text-align: right;">
    <div class="row">
        <!-- Title and Header Information -->
        <div class="col-12 mb-4">
            <h1 class="display-5 text-primary">{{ $questionnaire->Questionnaire->title }}</h1>
            <h3 class="mb-3">
                المادة: <span class="badge bg-secondary">{{ $questionnaire->CourseDetail->Course->name }}</span>
            </h3>
            <h4 class="mb-4">
                الدكتور: <span class="badge bg-secondary">{{ $questionnaire->CourseDetail->teaching_assistant_name }}</span>
            </h4>
            <h4 class="mb-4">
            إجمالي الردود:<span class="badge bg-success">{{ $stats['total_responses'] }}</span>
            </h4>
        </div>
    </div>

  
    <!-- Multiple Choice Questions Stats Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm p-4 rounded bg-light mb-5">
                <h3 class="text-secondary mb-4">إحصائيات الأسئلة متعددة الخيارات</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>السؤال</th>
                                <th colspan="{{ max(array_map(fn($q) => isset($q['stats']['percentages']) ? count($q['stats']['percentages']) : 0, $stats['questions'])) }}">النسب المئوية للإجابات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['questions'] as $questionStats)
                                @if($questionStats['type'] == 'multiple_choice' && isset($questionStats['stats']['percentages']))
                                    <tr>
                                        <td>{{ $questionStats['text'] }}</td>
                                        @foreach($questionStats['stats']['percentages'] as $option => $percentage)
                                            <td>{{ $option }} ({{ $percentage }}%)</td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Types of Questions -->
    <div class="row">
        @foreach($stats['questions'] as $questionStats)
            @if($questionStats['type'] != 'multiple_choice')
                <div class="col-12 mb-5">
                    <div class="card shadow-sm p-4 rounded bg-light">
                        <h3 class="text-secondary">السؤال: {{ $questionStats['text'] }}</h3>
                        <p>النوع: <span class="text-muted">{{ ucfirst($questionStats['type']) }}</span></p>

                        <!-- Text-Based Stats -->
                        @if($questionStats['type'] == 'text_based' && isset($questionStats['stats']))
                            <h4 class="mt-3">إحصائيات الإجابات النصية</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>نص الإجابة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questionStats['stats'] as $stat)
                                            <tr>
                                                <td>{{ $stat->answer_text }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

@endsection
