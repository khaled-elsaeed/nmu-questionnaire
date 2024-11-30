@extends('layouts.admin')

@section('title', 'Questionnaire Stats')

@section('content')
<div class="col-lg-12">
    <!-- Card for General Statistics -->
    <div class="card m-b-30">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">{{ $questionnaire->title }} - Overview</h5>
        </div>
        <div class="card-body">
            <h5 class="mb-4">General Stats</h5>

            @if($stats['total_responses'] == 0)
                <div class="alert alert-secondary">No responses yet.</div>
            @else
                <ul class="list-group mb-4">
                    <li class="list-group-item"><strong>Total Responses:</strong> {{ $stats['total_responses'] }}</li>
                    <li class="list-group-item"><strong>Text Responses:</strong> {{ $stats['text_based_responses'] }}</li>
                    <li class="list-group-item"><strong>Scaled Text Avg:</strong> 
                    @php
                        // Mapping the numeric average to the corresponding English description using mapArabicTextToEnglish
                        $scaledTextAvg = $stats['scaled_text_avg'];
                        $scaledTextAvgDescription = mapArabicTextToEnglish($scaledTextAvg);
                    @endphp

                    {{ $scaledTextAvgDescription }}
                </li>

                    <li class="list-group-item"><strong>Scaled Numerical Avg:</strong> 
                        {{ is_numeric($stats['scaled_numerical_avg']) ? number_format($stats['scaled_numerical_avg'], 2) : $stats['scaled_numerical_avg'] }}
                    </li>
                    <li class="list-group-item"><strong>Scale Average:</strong> 
                        {{ is_numeric($stats['scale_avg']) ? number_format($stats['scale_avg'], 2) : $stats['scale_avg'] }}
                    </li>
                </ul>
            @endif
        </div>
    </div>

    <!-- Card for Individual Question Statistics -->
    <div class="card m-b-30">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title">Question Stats</h5>
        </div>
        <div class="card-body">
            @foreach($question_stats as $qStat)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>{{ $qStat['question'] }}</strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Total Responses:</strong> {{ $qStat['stats']['total_responses'] }}</li>
                            <li class="list-group-item"><strong>Text Responses:</strong> {{ $qStat['stats']['text_based_responses'] }}</li>
                            <li class="list-group-item"><strong>Scaled Text Avg:</strong> 
                                @php
                                    // Mapping the numeric average to the corresponding English description using mapArabicTextToEnglish
                                    $scaledTextAvg = $qStat['stats']['scaled_text_avg'];
                                    
                                    // Check if the scaledTextAvg is numeric, then map it to English
                                    $scaledTextAvgDescription = mapArabicTextToEnglish($scaledTextAvg);
                                @endphp

                                {{ $scaledTextAvgDescription }}
                            </li>

                            <li class="list-group-item"><strong>Scaled Numerical Avg:</strong> 
                                {{ is_numeric($qStat['stats']['scaled_numerical_avg']) ? number_format($qStat['stats']['scaled_numerical_avg'], 2) : $qStat['stats']['scaled_numerical_avg'] }}
                            </li>
                            <li class="list-group-item"><strong>Scale Average:</strong> 
                                {{ is_numeric($qStat['stats']['scale_avg']) ? number_format($qStat['stats']['scale_avg'], 2) : $qStat['stats']['scale_avg'] }}
                            </li>
                            <li class="list-group-item"><strong>Scaled Text Responses Breakdown:</strong>
                                @foreach($qStat['stats']['scaled_text_counts'] as $key => $count)
                                    <span class="badge badge-info">{{ mapArabicTextToEnglish($key) }}: {{ $count }}</span>
                                @endforeach
                            </li>
                            <li class="list-group-item"><strong>Scaled Numerical Responses Breakdown:</strong>
                                @foreach($qStat['stats']['scaled_numerical_counts'] as $key => $count)
                                    <span class="badge badge-info">{{ $key }}: {{ $count }}</span>
                                @endforeach
                            </li>
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@php
    // Helper function to map Arabic scaled text answers to English descriptions
    function mapArabicTextToEnglish($answer) {
        $mapping = [
            '1' => 'Bad',
            '2' => 'Fair',
            '3' => 'Good',
            '4' => 'Very Good',
            '5' => 'Excellent'
        ];

        return $mapping[$answer] ?? 'Unknown';  
    }
@endphp