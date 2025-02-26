<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                {{-- dashboard --}}
                <li>
                    <a href="{{ url('/') }}"> <i class="feather-grid"></i><span>{{ __('dashboard') }}</span></a>
                </li>
                @if(auth()->user()->hasRole('Class Teacher') || auth()->user()->hasRole('Center'))
                <li>
                    <a href="{{ route('annual-project.show') }}"> <i class="feather-grid"></i><span>{{ __('annual_project') }}</span></a>
                </li>
                @endif
                @can('exam-upload-marks')

                    @if (isPrimaryCenter())
                        <li class="submenu">
                            <a href="#"> <i class="fa fa-book menu-icon"></i>
                                <span>{{ __('upload') . ' ' . __('Marks') }}</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a
                                        href="{{ route('competency.marks.index') }}">{{ __('upload') . ' ' . __('Competency') . ' ' . __('Marks') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('competency.marks.upload-student') }}">{{ __('upload') . ' ' . __('Student') . ' ' . __('Marks') }}</a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li><a href="{{ route('exams.sequential.upload-marks') }}"><i class="fa fa-book menu-icon"></i>
                                <span>{{ __('upload') . ' ' . __('Marks') }}</span></a></li>
                    @endif
                @endcan

                {{-- @hasrole('Super Admin') --}}
                @canany(['medium-create', 'section-create', 'class-create', 'subject-create',
                    'class-teacher-create', 'subject-teacher-list', 'subject-teacher-create', 'assign-class-to-new-student',
                    'promote-student-create', 'promote-student-list', 'section-edit', 'section-delete', 'section-list',
                    'subject-list', 'subject-edit', 'subject-delete', 'class-list', 'class-edit', 'class-delete',
                    'subject-teacher-edit'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-university menu-icon"></i>
                            <span>{{ __('academics') }}</span><span class="menu-arrow"></span> </a>
                        <ul>
                            @can('medium-create')
                                <li><a href="{{ route('medium.index') }}"> {{ __('medium') }} </a></li>
                            @endcan

                            @canany(['section-create', 'section-list', 'section-edit', 'section-delete'])
                                <li><a href="{{ route('section.index') }}"> {{ __('section') }} </a></li>
                            @endcanany

                            @if (isPrimaryCenter())
                                @can('competency-domain-list')
                                    <li><a href="{{ route('competency-domain.index') }}">{{ __('competency_domains') }}</a>
                                    </li>
                                @endcan
                                @can('competency-list')
                                    <li><a href="{{ route('competency.index') }}">{{ __('competency') }}</a></li>
                                @endcan
                                @can('competency-type-list')
                                    <li><a href="{{ route('competency-type.index') }}"> {{ __('competency_type') }} </a></li>
                                @endcan
                                @can('class-competency-list')
                                    <li><a href="{{ route('class-competency.index') }}">{{ __('Class Competencies') }}</a>
                                    </li>
                                @endcan
                                @can('competency-type-assign-class')
                                    <li><a href="{{ route('competency-type.assign-class') }}">
                                            {{ __('competency_type_assign_class') }} </a></li>
                                @endcan
                                {{-- <li><a href="{{ route('learning-units.index') }}">{{ __('Learning Units') }}</a></li> --}}
                            @endif


                            @if (!isPrimaryCenter())
                                @can(['subject-create', 'subject-list', 'subject-edit', 'subject-delete'])
                                    <li><a href="{{ route('subject.index') }}"> {{ __('subject') }} </a></li>
                                @endcanany

                                @canany(['department-create', 'department-list', 'department-edit', 'department-delete'])
                                    <li><a href="{{ route('department.index') }}"> {{ __('department') }} </a></li>
                                @endcanany
                                
                                @can('stream-create')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('stream.index') }}"> {{ __('stream') }} </a>
                                    </li>
                                @endcan

                                @can('shift-create')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('shifts.index') }}"> {{ __('shifts') }} </a>
                                    </li>
                                @endcan
                            @endif
                            @canany(['class-create', 'class-list', 'class-edit', 'class-delete'])
                                {{-- @if (auth()->user()->center->type == 'primary')
                                        <li><a href="{{ route('primary-class.index') }}"> {{ __('class') }} </a></li>
                                @else --}}
                                <li><a href="{{ route('class.index') }}"> {{ __('class') }} </a></li>
                                {{-- @endif --}}
                            @endcanany

                            @if (!isPrimaryCenter())
                                @canany(['class-group-create', 'class-group-list', 'class-group-edit',
                                    'class-group-delete'])
                                    <li><a href="{{ route('class-group.index') }}"> {{ __('Class Group') }} </a></li>
                                @endcanany


                                @can('assign-class-subject')
                                    <li><a href="{{ route('class.subject') }}">{{ __('assign_class_subject') }} </a></li>
                                @endcan
                            @endif

                            @can('assign-class-to-new-student')
                                <li><a href="{{ route('students.assign-class') }}">{{ __('assign_students_new_class') }}</a>
                                </li>
                            @endcan

                                @canany(['promote-student-create', 'promote-student-list'])
                                    <li><a href="{{ route('promote-student.index') }}">{{ __('promote_student') }}</a></li>
                                    <li><a href="{{ url('promoted-student') }}">{{ __('Promote Student List') }}</a></li>
                                @endcanany
                        </ul>
                    </li>
                @endcanany
                {{-- @endrole --}}

                {{-- student --}}
                @canany(['student-create', 'student-list', 'student-reset-password', 'class-teacher',
                    'student-id-card'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-graduation-cap menu-icon"></i>
                            <span>{{ __('students_and_parents') }}</span>
                            <span class="menu-arrow"></span> </a>
                        <ul>
                            @can('student-create')
                                <li><a href="{{ route('students.create') }}">{{ __('student_admission') }}</a></li>
                            @endcan

                            @can('student-create')
                                <li><a
                                        href="{{ route('students.index-students-roll-number') }}">{{ __('assign') }}{{ __('roll_no') }}</a>
                                </li>
                            @endcan

                            @canany(['student-list'])
                                <li><a href="{{ route('students.index') }}">{{ __('student_details') }}</a></li>
                            @endcanany

                            @canany(['parents-create', 'parents-list', 'parents-delete'])
                                <li><a href="{{ route('parents.index') }}"><span>{{ __('parent_details') }}</span> </a></li>
                            @endcanany

                            @can('generate-id-card')
                                <li><a href="{{ url('students/generate-id-card') }}">{{ __('Generate ID Card') }}</a></li>
                            @endcan

                            @can('student-reset-password')
                                <li><a
                                        href="{{ route('students.reset_password') }}">{{ __('students') . ' ' . __('reset_password') }}</a>
                                </li>
                            @endcan
                            @if (Auth::user()->hasRole('Center'))
                                <li><a href="{{ route('students.create-bulk-data') }}">{{ __('add_bulk_data') }}</a></li>
                            @endif
                        </ul>
                    </li>
                @endcanany

                @can('center-create')
                    <li><a href="{{ route('centers.index') }}"> <i
                                class="fa fa-building-user menu-icon"></i><span>{{ __('center') }}</span> </a></li>
                @endcan

                @can('center-create')
                    <li><a href="{{ route('centers.clone') }}"> <i
                                class="fa fa-building-user menu-icon"></i><span>{{ __('clone_center') }}</span>
                        </a></li>
                @endcan

                @canany(['super-teacher-create', 'super-teacher-list', 'super-teacher-edit', 'super-teacher-delete'])
                    <li><a href="{{ route('super.teacher.index') }}"><i
                                class="fa fa-user menu-icon"></i><span>{{ __('super_teacher') }}</span></a></li>
                @endcanany



                @canany(['course-create', 'course-list', 'course-edit', 'course-delete', 'course-report'])
                    <li>
                    <li><a href="{{ route('course_category.index') }}"><i
                                class="fa fa-book menu-icon"></i><span>{{ __('Course Category') }}</span></a></li>
                    </li>
                    <li class="submenu">
                        <a href="#"> <i class="fa-solid fa-arrow-right"></i><span>{{ __('course') }}</span></a>
                        <ul>
                            @canany(['course-create', 'course-list', 'course-edit', 'course-delete'])
                                <li><a href="{{ route('course.index') }}">{{ __('Create') }}</a></li>
                            @endcanany

                            @can('course-report')
                                <li><a href="{{ route('course.report') }}">{{ __('Report') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['teacher-create', 'teacher-list', 'teacher-delete'])
                    <li class="submenu">
                        <a href="#" style="text-align: center"> 
                            <i class="fa fa-user menu-icon"></i> 
                            <span>{{ __('teacher') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('teachers.index') }}">{{ __('Teacher Details') }}</a></li>
                            <li>
                                <a href="{{ route('teacher.reset-password.index') }}">
                                    {{ __('Reset Password Request') }}
                                    @if($resetRequestsCount > 0)
                                        <span class="badge badge-warning">{{ $resetRequestsCount }}</span>
                                    @endif
                                </a>
                            </li>
                            @can('class-teacher-create')
                                <li><a href="{{ route('class.teacher') }}">{{ __('assign_class_teacher') }}</a></li>
                            @endcan

                            @canany(['subject-teacher-list', 'subject-teacher-create', 'subject-teacher-edit',
                                'subject-teacher-delete'])
                                <li><a
                                        href="{{ route('subject-teachers.index') }}">{{ __('assign') . ' ' . __('subject') . ' ' . __('teacher') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- timetable --}}
                @canany(['timetable-create', 'class-timetable', 'teacher-timetable'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-calendar menu-icon"></i> <span>{{ __('timetable') }}</span>
                            <span class="menu-arrow"></span> </a>
                        <ul>
                            @can('timetable-create')
                                <li><a href="{{ route('timetable.index') }}">{{ __('create_timetable') }} </a></li>
                            @endcan

                            @canany(['class-timetable'])
                                <li><a href="{{ url('class-timetable') }}">{{ __('class_timetable') }}</a></li>
                            @endcanany

                            @can('teacher-timetable')
                                <li><a href="{{ url('teacher-timetable') }}">{{ __('teacher_timetable') }}</a></li>
                            @endcan

                            @canany(['timetable-create', 'timetable-settings'])
                                <li><a href="{{ route('timetable.settings') }}">{{ __('timetable_settings') }} </a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- subject lesson --}}
                @canany(['lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete', 'topic-list', 'topic-create',
                    'topic-edit', 'topic-delete'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-book menu-icon"></i> <span>{{ __('subject_lesson') }}</span>
                            <span class="menu-arrow"></span> </a>
                        <ul>
                            @canany(['lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete'])
                                <li><a href="{{ url('lesson') }}"> {{ __('create_lesson') }}</a></li>
                            @endcanany

                            @canany(['topic-list', 'topic-create', 'topic-edit', 'topic-delete'])
                                <li><a href="{{ url('lesson-topic') }}"> {{ __('create_topic') }}</a></li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @can('slider-create')
                    <li><a href="{{ route('sliders.index') }}"><i
                                class="fa fa-list menu-icon"></i><span>{{ __('sliders') }}</span></a></li>
                @endcan



                {{-- attendance --}}
                @canany(['attendance-create', 'attendance-edit', 'attendance-list'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-calendar-check menu-icon"></i>
                            <span>{{ __('attendance') }}</span><span class="menu-arrow"></span> </a>
                        <ul>
                            @canany(['attendance-create', 'attendance-edit'])
                                <li><a href="{{ route('attendance.index') }}">{{ __('add_attendance') }}</a></li>
                            @endcan

                            {{-- view attendance --}}
                            @can('attendance-list')
                                <li><a href="{{ route('attendance.view') }}">{{ __('view_attendance') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endrole
                {{-- @canany(['Class Teacher'])
                    <li class="submenu">
                        <a href="#">
                            <i class="fa fa-calendar-check menu-icon"></i>
                            <span>{{ __('attendance') }}</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul>
                            @can('attendance-create')
                                <li><a href="{{ route('attendance.index') }}">{{ __('add_attendance') }}</a></li>
                            @endcan

                            @can('attendance-list')
                                <li><a href="{{ route('attendance.view') }}">{{ __('view_attendance') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany --}}

                {{-- exam --}}
                @canany(['create-specific-exam', 'exam-timetable-create', 'exam-upload-marks', 'exam-result',
                    'exam-upload-marks', 'class-report', 'exam-sequence-create'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-book menu-icon"></i> <span>{{ __('exam') }}</span> <span
                                class="menu-arrow"></span> </a>
                        <ul>
                            <li class="submenu"><a href="javascript:void(0);" class="">
                                    <span>{{ __('Specific Exam') }}</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    @canany(['create-specific-exam', 'list-specific-exam'])
                                        <li><a href="{{ route('exams.index') }}"> {{ __('List Exam') }}</a></li>
                                    @endcan

                                    @canany(['exam-timetable-create', 'exam-timetable-list'])
                                        <li><a href="{{ route('exam-timetable.index') }}"> {{ __('Create Timetable') }}</a>
                                        </li>
                                    @endcanany

                                    @can('exam-upload-marks')
                                        <li><a
                                                href="{{ route('exams.specific.upload-marks') }}">{{ __('upload') . ' ' . __('Marks') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>

                            <li class="submenu"><a href="javascript:void(0);" class="" data->
                                    <span>{{ __('Sequential Exam') }}</span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    @can('list-sequential-exam')
                                        <li><a href="{{ route('exams.sequential.index') }}"> {{ __('List Exam') }}</a></li>
                                    @endcan
                                    @can('exam-upload-marks')
                                        <li><a
                                                href="{{ route('exams.sequential.upload-marks') }}">{{ __('upload') . ' ' . __('Marks') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>


                            @can('exam-result')
                                <li><a href="{{ route('exams.get-result') }}">{{ __('students') . ' ' . __('Result') }}</a>
                                </li>
                            @endcan

                            @can('exam-report')
                                @if (isPrimaryCenter())
                                    <li><a
                                            href="{{ route('competency-report.report-card-list') }}">{{ __('Report Card List') }}</a>
                                    </li>
                                @endif
                                @if (!isPrimaryCenter())
                                    @can('exam-report')
                                        <li><a href="{{ route('exam-report.index') }}">{{ __('Report') }}</a></li>
                                    @endcan
                                    @can('annual-report-card')
                                        <li><a href="{{ route('annual-report.index') }}">{{ __('annual_report') }}</a></li>
                                    @endcan
                                @endif
                            @endcan

                            @can('exam-report')
                                <li><a href="{{ route('exams.get-report') }}">{{ __('Report Statistics') }}</a></li>
                            @endcan

                            @can('class-report')
                                <li><a href="{{ url('class-report') }}">{{ __('Class Report') }}</a></li>
                            @endcan
                            {{-- <li><a href="{{ route('global-report.index') }}">{{ __('Global Report') }}</a></li> --}}
                            {{-- @can('annual-master-sheet')
                                <li><a href="{{ route('annual-master-sheets') }}">{{ __('annual_master_sheet') }}</a></li>
                            @endcan --}}
                            {{-- @can('best-report')
                                <li><a href="{{ url('annual-best-report') }}">{{ __('Best Report') }}</a></li>
                            @endcan --}}

                            @can('exam-sequence-create')
                                <li><a href="{{ url('exam-sequence-mark') }}">{{ __('Sequence Wise Marks') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['manage-online-exam'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-laptop menu-icon"></i><span>{{ __('online') }}
                                {{ __('exam') }}</span> <span class="menu-arrow"></span> </a>
                        <ul>
                            @can('manage-online-exam')
                                <li><a href="{{ route('online-exam.index') }}">
                                        {{ __('manage') }}{{ __('online') }}{{ __('exam') }}</a></li>
                                <li><a
                                        href="{{ route('online-exam-question.index') }}">{{ __('manage') . ' ' . __('questions') }}</a>
                                </li>
                                <li><a href="{{ route('online-exam.terms-conditions') }}"> {{ __('terms_condition') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Documents --}}
                @canany(['list-sequential-exam', 'exam-term-documents', 'statistics'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-print menu-icon"></i><span>{{ __('documents') }}</span> <span
                                class="menu-arrow"></span> </a>
                        <ul>
                            @can(['exam-term-documents'])
                                <li><a href="{{ route('et_documents') }}"> {{ __('et_documents') }}</a></li>
                            @endcan
                            @can('statistics')
                                <li><a href="{{ route('center_statistics') }}"> {{ __('statistics') }}</a></li>                                
                            @endcan

                        </ul>
                    </li>
                @endcanany

                {{-- Accounting --}}
                @canany(['fees-type', 'fees-classes', 'fees-paid', 'expense-create', 'expense-list', 'expense-edit',
                    'expense-delete', 'salary-paid', 'income-list', 'income-category'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-table menu-icon"></i><span>{{ __('accounting') }}</span>
                            <span class="menu-arrow"></span> </a>
                        <ul>
                            {{-- Fees --}}
                            @canany(['fees-type', 'fees-classes', 'fees-paid', 'fees-discount'])
                                <li class="submenu">
                                    <a href="#"> <i class="fa fa-dollar menu-icon"></i>
                                        <span>{{ __('fees') }}</span> <span class="menu-arrow"></span> </a>
                                    <ul>
                                        @can('fees-type')
                                            <li><a href="{{ route('fees-type.index') }}"> {{ __('fees') }}
                                                    {{ __('type') }}</a></li>
                                        @endcan

                                        @can('fees-classes')
                                            <li><a href="{{ route('fees.class.index') }}">{{ __('assign') }}{{ __('fees') }}{{ __('classes') }}
                                                </a></li>
                                        @endcan

                                        @can('fees-paid')
                                            <li><a href="{{ route('fees.paid.index') }}"> {{ __('fees') }}
                                                    {{ __('paid') }}</a></li>
                                        @endcan

                                        @can('fees-paid')
                                            <li><a href="{{ route('fees.transactions.log.index') }}">{{ __('fees') }}{{ __('transactions') }}
                                                    {{ __('logs') }}</a></li>
                                        @endcan
                                        @can('fees-discount-list')
                                            <li><a href="{{ route('fees.discounts.index') }}">{{ __('fee_discounts') }}</a></li>
                                        @endcan
                                        {{-- @can('fees-paid')
                                        <li >
                                            <a  href="{{ route('fees.receipt') }}"> {{__('fees')}} {{ __('receipt') }} {{__('logs')}}
                                            </a>
                                        </li>
                                        @endcan --}}
                                    </ul>
                                </li>
                            @endcanany

                            @canany(['income-list', 'income-category'])
                                <li class="submenu">
                                    <a href="#"> <i class="fa fa-dollar menu-icon"></i>
                                        <span>{{ __('Income') }}</span>
                                        <span class="menu-arrow"></span> </a>
                                    <ul>
                                        @can('income-list')
                                            <li><a href="{{ route('income.index') }}"> {{ __('income_list') }}</a></li>
                                        @endcan

                                        @can('income-category')
                                            <li><a href="{{ route('income-category.index') }}"> {{ __('income_category') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            {{-- Expenses --}}
                            @canany(['expense-create', 'expense-list', 'expense-edit', 'expense-delete', 'salary-paid'])
                                <li class="submenu">
                                    <a href="#"> <i class="fas fa-hand-holding-usd"></i>
                                        <span>{{ __('Expenses') }}</span> <span class="menu-arrow"></span> </a>
                                    <ul>
                                        @canany(['salary-paid'])
                                            <li><a href="{{ route('salary.index') }}"><span>{{ __('Salary') }}</span></a></li>
                                        @endcanany

                                        @canany(['expense-create', 'expense-list', 'expense-edit', 'expense-delete'])
                                            <li><a
                                                    href="{{ route('expense.index') }}"><span>{{ __('Other Expenses') }}</span></a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                {{-- Others --}}
                @canany(['announcement-create', 'announcement-list', 'announcement-edit', 'announcement-delete',
                    'form-field-create', 'assignment-create', 'assignment-submission', 'holiday-create', 'holiday-list',
                    'event-create', 'event-list'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-plus menu-icon"></i><span>{{ __('more') }}</span> <span
                                class="menu-arrow"></span> </a>
                        <ul>
                            {{-- announcement --}}
                            @canany(['announcement-create', 'announcement-list', 'announcement-edit',
                                'announcement-delete'])
                                <li><a href="{{ route('announcement.index') }}"> <i
                                            class="fa fa-check menu-icon"></i><span>{{ __('announcement') }}</span> </a></li>
                            @endcanany

                            @can('form-field-create')
                                <li><a href="{{ route('form-fields.index') }}"><i
                                            class="fa fa-list menu-icon"></i><span>{{ __('admission_form_field') }}</span></a>
                                </li>
                            @endcan

                            {{-- student assignment --}}
                            @canany(['assignment-create', 'assignment-submission'])
                                <li class="submenu">
                                    <a href="#"> <i class="fa fa-tasks menu-icon"></i>
                                        <span>{{ __('student_assignment') }}</span><span class="menu-arrow"></span> </a>
                                    <ul>
                                        @can('assignment-create')
                                            <li><a href="{{ route('assignment.index') }}">{{ __('create_assignment') }}</a>
                                            </li>
                                        @endcan
                                        @can('assignment-submission')
                                            <li><a
                                                    href="{{ route('assignment.submission') }}">{{ __('assignment_submission') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            {{-- Holiday --}}
                            @canany(['holiday-create', 'holiday-list'])
                                <li>
                                    @can('holiday-list')
                                        <a href="{{ route('holiday.index') }}"> <i
                                                class="fa fa-calendar-days menu-icon"></i><span>{{ __('holiday_list') }}</span>
                                        </a>
                                    @endcan
                                </li>
                            @endcanany

                            @canany(['event-create', 'event-list'])
                                <li>
                                    @can('event-list')
                                        <a href="{{ route('event.index') }}"> <i
                                                class="fa fa-calendar-days menu-icon"></i><span>{{ __('Event') }}</span> </a>
                                    @endcan
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                {{-- settings --}}
                @canany(['setting-create', 'session-year-create', 'fcm-setting-create', 'email-setting-create',
                    'privacy-policy', 'contact-us', 'about-us', 'role-create', 'role-list', 'role-edit', 'role-delete',
                    'user-create', 'user-list', 'user-edit', 'user-edit', 'grade-create', 'exam-term-create',
                    'exam-term-edit', 'exam-term-delete', 'exam-term-list', 'exam-sequence-create', 'exam-sequence-list'])
                    <li class="submenu">
                        <a href="#"> <i class="fa fa-cog menu-icon"></i> <span>{{ __('system_settings') }}</span>
                            <span class="menu-arrow"></span> 
                        </a>
                        <ul>
                            @if (Auth::user()->hasRole('Center') && isPrimaryCenter())
                            @endif

                            @can('app-setting-create')
                                <li><a href="{{ url('app-settings') }}">{{ __('app_settings') }}</a></li>
                            @endcan

                            @can('setting-create')
                                <li><a href="{{ url('settings') }}">{{ __('general_settings') }}</a></li>
                            @endcan

                            {{-- session-year --}}
                            @can('session-year-create')
                                <li><a href="{{ route('session-years.index') }}"> <i
                                            class="fa fa-calendar-week menu-icon"></i><span>{{ __('session_years') }}</span>
                                    </a></li>
                            @endcan

                            @canany(['grade-create', 'exam-term-create', 'exam-term-edit', 'exam-term-delete',
                                'exam-term-list', 'exam-sequence-create', 'exam-sequence-list'])
                                <li class="submenu"><a href="javascript:void(0);" class="">
                                        <span>{{ __('Exam Settings') }}</span> <span class="menu-arrow"></span></a>
                                    <ul style="display: none;">
                                        @can('grade-create')
                                            <li><a href="{{ route('grades') }}">{{ __('exam') }} {{ __('grade') }}</a>
                                            </li>
                                        @endcan

                                        @canany(['exam-result-subject-group-create', 'exam-result-subject-group-list'])
                                            <li><a
                                                    href="{{ route('result-subject-group.index') }}">{{ __('Result Subject Group') }}</a>
                                            </li>
                                        @endcanany

                                        @canany(['exam-term-create', 'exam-term-list'])
                                            <li><a href="{{ route('exam-terms.index') }}">{{ __('Exam Term') }}</a></li>
                                        @endcan

                                        @canany(['exam-sequence-create', 'exam-sequence-list'])
                                            <li><a href="{{ route('exam-sequences.index') }}">{{ __('Exam Sequence') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany


                            <li class="submenu">
                                <a href="#"> <span>{{ __('Report and Class Type Settings') }}</span>
                                    <span class="menu-arrow"></span> 
                                </a>
                                <ul>
                                    @can('exam-report')
                                        <li><a href="{{ route('report-settings.index') }}">{{ __('Report Settings') }}</a></li>
                                    @endcan
                                    @can('class-edit')
                                        <li><a href="{{ route('class.report-edit') }}">{{ __('class_type_settings') }} </a></li>
                                    @endcan
                                </ul>
                            </li>
                            
                            {{--                            @can('language-create') --}}
                            {{--                                <li><a href="{{ url('language') }}">{{ __('language_settings') }}</a></li> --}}
                            {{--                            @endcan --}}
                            
                            @can('fcm-setting-create')
                                <li><a href="{{ url('fcm-settings') }}"> {{ __('fcm_key') }}</a></li>
                            @endcan

                            {{--                            @can('fees-config') --}}
                            {{--                                <li> --}}
                            {{--                                    <a href="{{ route('fees.config.index') }}">{{ __('fees') }} {{ __('configration') }}</a> --}}
                            {{--                                </li> --}}
                            {{--                            @endcan --}}

                            @can('email-setting-create')
                                <li><a href="{{ url('email-settings') }}">{{ __('email_configuration') }}</a></li>
                            @endcan

                            @can('privacy-policy')
                                <li><a href="{{ url('privacy-policy') }}">{{ __('privacy_policy') }}</a></li>
                            @endcan

                            @can('contact-us')
                                <li><a href="{{ url('contact-us') }}"> {{ __('contact_us') }}</a></li>
                            @endcan

                            @can('about-us')
                                <li><a href="{{ url('about-us') }}"> {{ __('about_us') }}</a></li>
                            @endcan

                            @can('terms-condition')
                                <li><a href="{{ url('terms-condition') }}">{{ __('terms_condition') }}</a></li>
                            @endcan

                            @canany(['user-create', 'user-list', 'user-edit', 'user-delete'])
                                <li><a href="{{ url('users/') }}"> {{ __('user') }}</a></li>
                            @endcan

                            @canany(['role-create', 'role-list', 'role-edit', 'role-delete'])
                                <li><a href="{{ url('roles/') }}"> {{ __('role_permission') }}</a></li>
                            @endcan
                        </>
                    </li>
                @endcanany

                {{--                @if (Auth::user()->hasRole('Super Admin')) --}}
                {{--                    <li><a href="{{ route('system-update.index') }}"> <i class="fa fa-cloud-download menu-icon"></i><span>{{ __('system_update') }}</span> </a></li> --}}
                {{--                @endif --}}

            </ul>
        </div>
    </div>
</div><!-- /Sidebar -->
