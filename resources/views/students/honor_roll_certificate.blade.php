<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Honor Roll</title>
    <style>
        .content {
            border: 1px solid black;
            height: 870px;
        }

        .header {
            width: 100%;
            height: 140px;
            text-align: center;
            margin-top: -10px;
        }

        .title {
            font-size: 14px;
        }

        .body {
            text-align: center;
            margin-top: 2rem;
            height: 600px;
        }

        .certify {
            font-weight: 700;
            font-size: 30px;
        }

        .school-name {
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 3rem;
        }

        .student-name {
            font-size: '50px';
            margin-top: 1rem;
            font-weight: bold;
            margin-left: 10%;
            margin-right: 10%;
            border-bottom: 1px solid black;
        }

        .main-body {
            margin: 0 10%;
            margin-top: 1rem;
            line-height: 2rem;
            font-size: 18px;
        }

        .student-detail-table {
            font-size: 20px;
            margin-top: 2rem;
            width: 70%;
            margin-left: 15%;
            margin-right: 15%;
        }

        table,
        th,
        td {
            border-collapse: collapse;
            line-height: 2rem;
        }

        table td {
            text-align: left;
        }

        .footer-table {
            width: 90%;
            margin-left: 5%;
            margin-right: 5%;
            margin-top: 10rem;
            font-weight: 600;
        }

        .date {
            position: absolute;
            margin-left: 10%;
        }

        .date-line {
            border-top: 1px solid black;
            width: 130px;
            margin-left: -48px;
            margin-bottom: 0.5rem;
        }

        .signature {
            position: absolute;
            margin-left: 73%;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 130px;
            margin-left: -30px;
            margin-bottom: 0.5rem;
        }

        .page-break {
            page-break-after: always;
        }

        .logo {
            position: absolute;
            text-align: center;
            width: 100%;
            opacity: 0.3;
            margin-top: 5rem;
        }

        .footer {
            bottom: 60px;
            width: 90%;
            margin-left: 5%;
            margin-right: 5%;
            font-weight: 600;
        }
    </style>
</head>

<body>
@foreach ($exam_report_detail as $exam_report)
    <div class="content">
        <div class="header">
            @if(file_exists(public_path('storage/'.$school_logo)) && is_file(public_path('storage/'.$school_logo)))
                <div class="logo">
                    <img src="{{ public_path('storage/' . $school_logo) }}"
                         alt="" height="500">
                </div>
            @endif
        </div>

        <div class="body">

            <div class="main-body" style="font-size: 20px">
                @php
                    $certificate_text = $template_data['certificate_text'] ?? '';

                    // Replace shortcodes with actual values
                    $replacements = [
                        '{{school_logo}}' => '',
                        '{{school_name}}' => $school_name,
                        '{{student_name}}' => $exam_report->student->user->full_name,
                        '{{session_year}}' => $exam_report->exam_report->session_year->name,
                        '{{exam_term}}' => $exam_report->exam_report->exam_term->name,
                        '{{class_section}}' => $exam_report->student->class_section->class->name . ' - ' . $exam_report->student->class_section->section->name,
                        '{{rank}}' => $exam_report->rank,
                        '{{avg}}' => $exam_report->avg
                    ];

                    foreach ($replacements as $shortcode => $value) {
                        $certificate_text = str_replace($shortcode, $value, $certificate_text);
                    }
                @endphp

                {!! $certificate_text !!}
            </div>

        </div>

    </div>
@endforeach
</body>
</html>