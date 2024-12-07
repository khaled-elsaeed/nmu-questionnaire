@extends('layouts.admin')

@section('content')
<!-- Back to Modules Button -->
<div class="col-lg-12 mb-4">
    <a href="{{ route('admin.questionnaires.results') }}" class="btn btn-primary btn-sm rounded-pill">
        <i class="fa fa-arrow-left mr-2"></i> Back to results
    </a>
</div>
<div class="container mt-5" style="direction: rtl; text-align: right;">
    <h1 class="display-6 text-primary">الإحصائيات لهدف الاستبيان: {{ $questionnaire->Questionnaire->title }}</h1>
    <h3>المادة: <span class="badge bg-secondary">{{ $questionnaire->CourseDetail->Course->name }}</span></h3>
    <h3>الدكتور: <span class="badge bg-secondary">{{ $questionnaire->CourseDetail->teaching_assistant_name }}</span></h3>

    <!-- Total Responses -->
    <div class="mb-4">
        <h3>إجمالي الردود: <span class="badge bg-secondary">{{ $stats['total_responses'] }}</span></h3>
    </div>

    <!-- Table for All Multiple Choice Questions -->
    <div class="multiple-choice-section mb-5 p-3 border rounded shadow-sm">
        <h3 class="text-secondary">إحصائيات الأسئلة متعددة الخيارات</h3>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-light">
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
                                    <td>{{ $option }} ( {{ $percentage }}%)</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Other Questions -->
    @foreach($stats['questions'] as $questionStats)
        @if($questionStats['type'] != 'multiple_choice')
            <div class="question-section mb-5 p-3 border rounded shadow-sm">
                <h3 class="text-secondary">السؤال: {{ $questionStats['text'] }}</h3>
                <p>النوع: <span class="text-muted">{{ ucfirst($questionStats['type']) }}</span></p>

                <!-- Text-Based Stats -->
                @if($questionStats['type'] == 'text_based' && isset($questionStats['stats']))
                    <h4 class="mt-3">إحصائيات الإجابات النصية</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-light">
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

                <!-- Scaled Text Stats -->
                @if($questionStats['type'] == 'scaled_text' && isset($questionStats['stats']))
                    <h4 class="mt-3">إحصائيات النصوص ذات النطاق</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>القيمة</th>
                                    <th>عدد الإجابات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionStats['stats'] as $stat)
                                    <tr>
                                        <td>{{ $stat->scale_value }}</td>
                                        <td>{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Scaled Numerical Stats -->
                @if($questionStats['type'] == 'scaled_numerical' && isset($questionStats['stats']))
                    <h4 class="mt-3">إحصائيات القيم الرقمية ذات النطاق</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>القيمة</th>
                                    <th>عدد الإجابات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionStats['stats'] as $stat)
                                    <tr>
                                        <td>{{ $stat->value }}</td>
                                        <td>{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Scale Stats -->
                @if($questionStats['type'] == 'scale' && isset($questionStats['stats']))
                    <h4 class="mt-3">إحصائيات النطاق</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>القيمة</th>
                                    <th>عدد الإجابات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionStats['stats'] as $stat)
                                    <tr>
                                        <td>{{ $stat->scale_value }}</td>
                                        <td>{{ $stat->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    @endforeach
</div>

@endsection
