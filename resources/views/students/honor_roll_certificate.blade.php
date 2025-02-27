<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Honor Roll Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            background-image: url("{{ public_path($template_data['background_image_path']) }}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            width: 100%;
            height: 100%;
        }
        .certificate-container {
            width: 100%;
            height: 100vh;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }
        /* Preserve all styles from the editor */
        .certificate-content {
            position: relative;
            z-index: 1;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        /* Additional styles to ensure editor content is preserved */
        .certificate-content h1,
        .certificate-content h2,
        .certificate-content h3,
        .certificate-content h4,
        .certificate-content h5,
        .certificate-content h6,
        .certificate-content p,
        .certificate-content div,
        .certificate-content table {
            margin: inherit;
            padding: inherit;
            font-family: inherit;
            font-size: inherit;
            text-align: inherit;
            line-height: inherit;
            color: inherit;
        }
        /* Ensure images are displayed correctly */
        .certificate-content img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
@foreach ($exam_report_detail as $exam_report)
    <div class="certificate-container">
        <!-- Alternative background method as a fallback -->
        @if(isset($template_data['background_image_path']) && !empty($template_data['background_image_path']))
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;">
                <img src="{{ public_path($template_data['background_image_path']) }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
            </div>
        @endif

        <div class="certificate-content">
            @php
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
                    '{{avg}}' => $exam_report->avg
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