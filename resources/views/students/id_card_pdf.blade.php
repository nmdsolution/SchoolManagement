<!DOCTYPE html>
<html>
    {{-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> --}}
<head>
    <meta charset="UTF-8">

    <title>Student ID Card</title>
    <style type="text/css">
        body,
        .content {
            margin-left: 0px;
            margin-right: 0px;
        }

        .header-table {
            width: 100%;
            margin: 0px;
            height: 30px;
            height: 50px;
            
        }

        .header {
            height: 70px;
            background-color: {{ $header_color }};
        }

        table,
        th,
        td {
            border-collapse: collapse;
            line-height: 1rem;
        }

        .logo-name {
            /* background-color: {{ $header_color }}; */
            
        }

        .school-logo {
            padding-left: 10px;
            text-align: left;
            height: 60px;
            padding-top: 5px;
        }

        .school-name {
            text-align: right;
            padding-right: 1rem;
            font-size: 14px;
            color: {{ $font_color }};
        }

        .tag-name {
            text-align: center;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .profile {
            height: 90px;
            /* width: 130px; */
            margin-left: 10px;
            object-fit: cover;
            border: 3px solid {{ $header_color }};
            
        }

        .student-data {
            margin-top: -110px;
            margin-left: 140px;
            text-align: left;
        }

        .student-detail {
            height: 115px;
        }

        .footer {
            height: 20px;
            /* margin-top: -10px; */
            background-color: {{ $footer_color }}
        }

        .page-break {
            /* page-break-after: always; */
            page-break-before: always;
        }

        .right-side {
            text-align: end;
            padding-right: 10px;
            padding-top: 5px;
            letter-spacing: 2px;
            font-size: 11px;
            color: {{ $font_color }};
        }

        .yadiko-img {
            position: fixed;
            margin-top: -5px;
            height: 30px;
        }
        .water-mark {
            position: fixed;
            margin-left: 60px;
            width: 100%;
            text-align: center;
            padding-right: 10px;
        }
        .water-mark-img {
            height: 110px;
            width: auto;
            margin-top: -5px;
            opacity: 0.2;
            padding-right: 10px;
        }
    </style>
</head>

<body>
    @foreach ($students as $student)
        <div class="content">
            <div class="header">
                <table class="header-table">
                    <tr class="logo-name">
                        <th width="70">
                            <img class="school-logo" src="{{ public_path($school_detail['logo']) }}">
                        </th>
                        <th class="school-name">
                            <h3>{{ $school_detail['name'] }}</h3>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="tag-name">
                <h5 style="margin: 10px 0px">{{ __('Student Identification Card') }}</h5>
            </div>
            <div class="water-mark">
                <img src="{{ public_path($school_detail['water_mark']) }}" class="water-mark-img">
            </div>
            <div class="student-detail">
                <div class="student-image">
                    @if ($student->user->image)
                        <img class="profile" src="{{ public_path('storage/' . $student->user->getRawOriginal('image')) }}">
                    @else
                        <img class="profile" src="{{ public_path('assets/img/no_image_available.jpg') }}">
                    @endif
                </div>
                <div class="student-data">
                    <table style="width:100%;font-size:11px">
                        @foreach ($student_id_fields as $student_id_field)
                            <tr>
                                <th>{{ $student_id_field == 'admission_no' ? __('matricule') : __($student_id_field) }}
                                    :</th>
                                <td style="width: 68%">
                                    {{ 
                                        $student_id_field == 'admission_no' ? 
                                            $initial_code . $student->$student_id_field 
                                        : ($student_id_field == 'nationality' ?
                                                trans($student->$student_id_field)
                                            :
                                            $student->$student_id_field) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <img class="yadiko-img" src="{{ public_path('assets/img/yadiko.png') }}">
            <div class="footer">
                <div class="right-side">
                    <b style="padding-right: 10px;">{{ __('Valid until') }} {{ $school_detail['valid'] }}</b>
                </div>
            </div>
        </div>
        
        @if (($loop->index + 1 != count($students)))
            
        @else
            <div style="margin-bottom: -20px">

            </div>
        @endif

        {{-- <div class="page-break">
            
        </div> --}}

    @endforeach
</body>

</html>
