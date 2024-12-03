@extends('layouts.responder')

@section('title', $questionnaire->title)

@section('links')
<link rel="stylesheet" href="{{ asset('plugins/ion-rangeSlider/ion.rangeSlider.css') }}">

<style>
    .title-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .question-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        padding: 20px;
        direction: rtl;
        text-align: right;
    }

    .question-card:hover {
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .question-text {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .form-control, .form-check-input {
        border-radius: 5px;
        border: 1px solid #ced4da;
    }

    .form-control:focus, .form-check-input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    @media (max-width: 768px) {
        .question-container {
            padding-left: 10px;
            padding-right: 10px;
        }

        .btn-submit, .btn-back {
            padding: 8px 16px;
        }

        .question-card {
            padding: 15px;
        }

        .title-card {
            padding: 15px;
        }
    }

    .irs-bar {
        height: 8px;
        top: 25px;
        background-position: 0 -60px;
    }

    .irs-bar-edge {
        top: 25px;
        height: 8px;
        width: 10px;
        background-position: 0 -90px;
        border-radius: 15px 0 0 15px;
        background: none !important;
        background-color: #8C2F39 !important;
    }

    .irs-bar {
        background: none !important;
        background-color: #8C2F39 !important;
    }

    .form-check .form-check-input {
        float: right !important;
        margin-left:10px;
    }
    

    
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="title-card">
                <h2 class="text-primary text-center">{{ $questionnaire->title }}</h2>
            </div>
        </div>
    </div>

    <div class="row mb-3">
    <div class="col-12 text-left">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('responder.home') }}'">
            <i class="fa fa-arrow-left"></i> Back to Questionnaires
        </button>
    </div>
</div>


    <form action="{{ route('responder.questionnaire.submit', $questionnaire->id) }}" method="POST">
        @csrf
        <input type="hidden" name="target_id" value="{{ $targetId }}">

        @foreach($questionnaire->questions as $question)
            <div class="question-container">
                <div class="question-card">
                    <div class="card-body">
                        <h5 class="question-text">{{ $loop->iteration }}. {{ $question->text }}</h5>

                        @if($question->type === 'multiple_choice')
                            @foreach($question->options as $option)
                                <div class="form-check">
                                    <input type="radio" name="answers[{{ $question->id }}]" id="option_{{ $option->id }}" value="{{ $option->id }}" class="form-check-input" required>
                                    <label for="option_{{ $option->id }}" class="form-check-label">
                                        {{ $option->text }}
                                    </label>
                                </div>
                            @endforeach
                        @elseif($question->type === 'text_based')
                            <textarea name="answers[{{ $question->id }}]" rows="3" class="form-control" placeholder="أدخل إجابتك هنا..." required></textarea>
                        @elseif($question->type === 'scaled_numerical')
                            <label for="scale_{{ $question->id }}" class="form-label">اختر تقييماً(من 1 إلى 5):</label>
                            <input name="answers[{{ $question->id }}]" id="range-slider-own-numbers" data-question-id="{{ $question->id }}" required>
                        @elseif($question->type === 'scaled_text')
                            <label for="scale_{{ $question->id }}" class="form-label">اختر تقييماً(من 1 إلى 5):</label>
                            <input id="range-slider-string-value" name="answers[{{ $question->id }}]" data-question-id="{{ $question->id }}">
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="fa fa-check-circle"></i> Submit Answers
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('plugins/ion-rangeSlider/ion.rangeSlider.min.js') }}"></script>

<script>
    document.querySelectorAll('#range-slider-own-numbers').forEach(function(slider) {
        $(slider).ionRangeSlider({
            type: "single",
            min: 1,
            max: 5,
            from: 0,
            step: 1,
            grid: true
        });
    });


    document.querySelectorAll('#range-slider-string-value').forEach(function(slider) {
        $(slider).ionRangeSlider({
            grid: true,
            from: 0,
            values: [
                "ضعيف", "مقبول", "جيد", "جيد جدا", "ممتاز"
            ]
        });
    });


</script>
@endsection
