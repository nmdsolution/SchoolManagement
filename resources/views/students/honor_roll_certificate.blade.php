<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $template_data['certificate_title'] ?? 'Certificate' }}</title>
    <style>
        /* Reset and base styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            width: {{ $template_data['width'] }}mm;
            height: {{ $template_data['height'] }}mm;
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
        }

        /* Background image styling */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            z-index: -1;
        }
        .certificate-container {
            page-break-before: always; /* Forces each certificate to start on a new page */
            page-break-inside: avoid;
            width: 100%;
            height: 99.9%;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }
        /* Certificate content container */
        .certificate-content {
            position: absolute;
            width: 100%;
            height: 100%;
            padding: 20px;
            z-index: 2;
            top: 5%;
            transform: scale(6.4);
            transform-origin: center top;
        }

        /* Custom CSS from template */
        {!! $template_data['custom_css'] ?? '' !!}
    </style>
</head>
<body>

@foreach ($exam_report_detail as $exam_report)
    <div class="certificate-container">
        <!-- Alternative background method as a fallback -->
        @if(isset($template_data['background_image_path']) && !empty($template_data['background_image_path']))
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;">
                <img src="{{ public_path($template_data['background_image_path']) }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 10; left: 0;">
            </div>
        @endif

        <div class="certificate-content">
            @php
                // Get the predefined honor roll text from settings
                     $student_honor_roll_text = getSettings('student_honor_roll_text');
                     $predefined_honor_roll_text = $student_honor_roll_text['student_honor_roll_text'] ?? 'Default honor roll text';

                  $certificate_text = $template_data['certificate_text'] ?? '';

                  // Replace shortcodes with actual values
                  $replacements = [
                      '{{school_logo}}' => file_exists(public_path('storage/'.$school_logo)) && is_file(public_path('storage/'.$school_logo))
                          ? '<img src="' . public_path('storage/' . $school_logo) . '" alt="School Logo" style="max-height: 100px;">'
                          : '',
                      '{{school_name}}' => $school_name,
                      '{{student_name}}' => $exam_report->student->user->full_name,
                      '{{session_year}}' => $exam_report->exam_report->session_year->name,
                      '{{exam_term}}' => $exam_report->exam_report->exam_term->name,
                      '{{class_section}}' => $exam_report->student->class_section->class->name . ' - ' . $exam_report->student->class_section->section->name,
                      '{{rank}}' => $exam_report->rank,
                      '{{avg}}' => $exam_report->avg,
                       '{{honor_roll_text}}' => $predefined_honor_roll_text
                  ];

                  // Process all replacements
                  foreach ($replacements as $shortcode => $value) {
                      $certificate_text = str_replace($shortcode, $value, $certificate_text);
                  }
            @endphp
            {!! $certificate_text !!}
</div>
    </div>
@endforeach
</body>
</html>