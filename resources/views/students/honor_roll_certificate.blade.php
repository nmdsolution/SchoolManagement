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
                <div class="school-name">
                    <h1>{{ $school_name }}</h1>
                </div>
                @if(file_exists(public_path('storage/'.$school_logo)) && is_file(public_path('storage/'.$school_logo)))
                <div class="logo">
                    <img src="{{ public_path('storage/' . $school_logo) }}"
                        alt="" height="500">
                        
                </div>
                @endif

                <div class="title">
                    <h1>{{ __('STUDENT HONOR ROLL CERTIFICATE') }}</h1>
                </div>
            </div>

            <div class="body">
                <div class="certify">
                    {{ __('This is certify that') }}
                </div>
                <div class="student-name" style="font-size: 40px; margin-top: 2rem">
                    {{ $exam_report->student->user->full_name }}
                </div>
                <div class="main-body" style="font-size: 20px">
                    @if ($student_honor_roll_text)
                        {{ $student_honor_roll_text['student_honor_roll_text'] }}
                    @else
                    a dedicated and diligent student, has earned a well-deserved place on the Honor Roll. Throughout the academic year, [he/she] consistently exhibited exceptional commitment to academic excellence and demonstrated an impressive work ethic. [His/Her] outstanding performance in [specific subjects or courses] reflects [his/her] unwavering dedication to learning and growth. [His/Her] exemplary behavior, both inside and outside the classroom, sets a positive example for peers and showcases [his/her] strong character and leadership qualities. It is with great pride and admiration that we award this Honor Roll certificate in recognition of [his/her] remarkable achievements and commitment to excellence. Congratulations on this well-earned accomplishment!    
                    @endif
                    
                </div>
                <div class="student-detail" style="margin-top: 2rem;">
                    <table class="student-detail-table">
                        <tr>
                            <th>{{ __('session_years') }} : </th>
                            <td>{{ $exam_report->exam_report->session_year->name }}</td>
                            <th>{{ __('Term') }} : </th>
                            <td>{{ $exam_report->exam_report->exam_term->name }}</td>
                        </tr>
                        <tr>
                            <th>{{__('class_section')}} : </th>
                            <td>{{ $exam_report->student->class_section->class->name }} -
                                {{ $exam_report->student->class_section->section->name }}</td>
                            <th>{{ __('Rank') }} : </th>
                            <td>{{ $exam_report->rank }}</td>
                        </tr>
                        <tr>
                            <th>{{__('Average')}} : </th>
                            <td>{{ $exam_report->avg }} / 20</td>
                            <th></th>
                            <td></td>
                        </tr>
                    </table>
                </div>
                {{-- Student name, Class section, Term, Average, Rank, Session Year --}}
            </div>
            <div class="footer" style="font-zise: 18px;">
                <div class="date">
                    <div class="date-line">

                    </div>
                    {{ __('date') }}
                </div>
                <div class="signature">
                    <div class="signature-line">

                    </div>
                    {{ __('Signature') }}
                </div>
            </div>
            
        </div>
    @endforeach

</body>

</html>
