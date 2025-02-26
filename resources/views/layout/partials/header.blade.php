<!-- Header -->
@if (Session::get('locale'))
    {{ app()->setLocale(Session::get('locale')) }}
@endif
@php
    current_language();

    // Modify the route to include filtering parameters
    $url = route('exam-sequences.show', [1, 'active' => 1, 'current' => 1]);

    // Simplified columns for current active sequences
    $columns = [
        trans('name')=>['data-field'=>'name'],
        trans('Term')=>['data-field'=>'term'],
        trans('start_date')=>['data-field'=>'start_date'],
        trans('end_date')=>['data-field'=>'end_date'],
          trans('status')=>['data-field'=>'status','data-formatter'=>'statusFormatter'],
    ];

    // Maintain action column but only for edit
    $actionColumn = [
        'editButton'=> ['url'=>url('exam-sequences')],
        'data-events'=>'ExamSequenceEvents'
    ];

    // Add query parameters to filter data
    $queryParams = [
        'pageSize' => 1,  // Limit to 5 most recent sequences
        'sortOrder' => 'desc',
        'sortName' => 'start_date',
        'status' => 1  // Only active sequences
    ];
@endphp

<div class="header">
    <!-- Logo -->
    <div class="header-left justify-content-center">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ url('images/vertical_logo.png')  }}"
                 alt="Logo">
        </a>
        {{-- <a href="{{ url('/') }}" class="logo logo-small">
            <img src="{{ getSettings('logo2') ? url(Storage::url(getSettings('logo2')['logo2'])) : url('assets/logo.svg') }}"
                 alt="Logo" class="w-100">
        </a> --}}
    </div>
    <!-- /Logo -->
    <div class="menu-toggle">
        <a href="javascript:void(0);" id="toggle_btn"> <i class="fas fa-bars"></i> </a>
    </div>
    <!-- Search Bar -->
{{-- <div class="top-nav-search">
    <form>
        <input type="text" class="form-control" placeholder="Search here">
        <button class="btn" type="submit"><i class="fas fa-search"></i></button>
    </form>
</div> --}}
<!-- /Search Bar -->

    <!-- Mobile Menu Toggle -->
    <a class="mobile_btn" id="mobile_btn"> <i class="fas fa-bars"></i> </a>
    <!-- /Mobile Menu Toggle -->

    <!-- Header Right Menu -->

    @php
        $super_admin_status = 1;
        $teacher_status = 1;
    @endphp

    <ul class="nav user-menu">

        @if (Auth::user()->teacher)
            {{-- <li class="nav-item dropdown me-2">
                <a href="#" class="dropdown-toggle nav-link header-nav-list active_center"
                   data-bs-toggle="dropdown">
                    <img class="rounded-circle" src="{{ active_center('logo') }}" width="35" alt=""><span
                            class="center_name">{{ active_center('name') }}</span>
                </a>
                <div class="dropdown-menu">
                    @foreach (get_teacher_center() as $center)
                        <a class="dropdown-item" href="{{ url('set-center', $center->center->id) }}">
                            <img class="rounded-circle" width="25" src="{{ $center->center->logo }}" alt="">
                            <span class="center_name">{{ $center->center->name }}</span>
                        </a>
                    @endforeach
                    @if (Auth::user()->staff->where('center_id', null)->first())
                        <a class="dropdown-item" href="{{ url('set-user-center', -1) }}"><img class="rounded-circle"
                                                                                              width="25" src="" alt="">
                            <span class="center_name">Super Admin
                                Panel</span></a>
                        @php
                            $super_admin_status = 0;
                        @endphp
                    @endif
                </div>
            </li> --}}
        @endif


        @if (Auth::user()->staff->first() && !Auth::user()->teacher)
            <li class="nav-item dropdown me-2">
                @if ($super_admin_status == 1)
                    <a href="#" class="dropdown-toggle nav-link header-nav-list active_center"
                       data-bs-toggle="dropdown">
                        <img class="rounded-circle" src="{{ user_active_center('logo') }}" width="35"
                             alt=""><span class="center_name">{{ user_active_center('name') }}</span>
                    </a>
                @endif

                <div class="dropdown-menu">
                    @foreach (get_user_center() as $center)
                        <a class="dropdown-item" href="{{ url('set-user-center', $center->id) }}"><img
                                    class="rounded-circle" width="25" src="{{ $center->logo }}" alt=""> <span
                                    class="center_name">{{ $center->name }}</span></a>
                    @endforeach
                    @if (Auth::user()->staff->where('center_id', null)->first() && $super_admin_status == 1)
                        <a class="dropdown-item" href="{{ url('set-user-center', -1) }}"><img class="rounded-circle"
                                                                                              width="25" src="" alt="">
                            <span class="center_name">Super Admin
                                Panel</span></a>
                    @endif

                </div>
            </li>
    @endif
    <!-- Exam Sequences Section html-->
        <li class="nav-item dropdown me-2">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle nav-link"  data-bs-toggle="dropdown">
                    {{ __('Exam Sequences') }}
                </a>
                <div class="dropdown-menu">
                    <div class="p-2" style="width: 400px;">
                        <div class="col-12">
                            <ul class="list-group exam-sequences-list" id="examSequencesList">
                                <!-- Data will be populated here via JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </li>

        <style>
            .exam-sequences-list {
                max-height: 400px;
                overflow-y: auto;
            }

            .exam-sequences-list .list-group-item {
                padding: 0.5rem;
                border-radius: 4px;
            }

            .exam-sequences-list .list-group-item:hover {
                background-color: #f8f9fa;
            }

            .sequence-info {
                flex: 1;
            }

            .sequence-info h6 {
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
            }

            .sequence-info .small {
                font-size: 0.75rem;
            }

            .sequence-dates {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .dropdown-menu {
                padding: 0;
            }

            .sequence-actions {
                opacity: 0.7;
                transition: opacity 0.2s;
            }

            .list-group-item:hover .sequence-actions {
                opacity: 1;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dropdownToggle = document.querySelector('.dropdown-toggle');

                dropdownToggle.addEventListener('click', function() {
                    fetchExamSequences();
                });

                function fetchExamSequences() {
                    fetch('{{ $url }}')
                        .then(response => response.json())
                        .then(data => {
                            const listContainer = document.getElementById('examSequencesList');
                            listContainer.innerHTML = '';

                            data.rows.forEach(sequence => {
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item d-flex justify-content-between align-items-start p-2 border-bottom';

                                listItem.innerHTML = `
                        <div class="sequence-info">
                            <h6>${sequence.name}</h6>
                            <div class="sequence-dates text-muted small">
                                <div><i class="bi bi-calendar3"></i> ${sequence.term}</div>
                                <div><i class="bi bi-clock"></i> ${sequence.start_date} - ${sequence.end_date}</div>
                                <span class="badge ${sequence.status ? 'bg-success' : 'bg-secondary'}">
                                    ${sequence.status ? '{{ __("Active") }}' : '{{ __("Inactive") }}'}
                                </span>
                            </div>
                        </div>
                    `;

                                listContainer.appendChild(listItem);
                            });
                        })
                        .catch(error => console.error('Error fetching exam sequences:', error));
                }
            });
        </script>
        <ul class="nav user-menu">

            {{-- @hasanyrole(['Center', 'Teacher']) --}}
            @if (getCurrentMedium()->name)
                @if (Auth::user()->center or
                        Auth::user()->teacher or
                        Auth::user()->staff()->first())
                    <div class="d-flex items-center flex-col mx-3 d-none d-md-block">
                        {{ getSessionName()  }}
                    </div>
                    <b class="language-drop">{{__('Sector')}} :</b>&nbsp;&nbsp;
                    <li class="nav-item dropdown me-2">
                        <a href="#" class="dropdown-toggle nav-link header-nav-list medium-name"
                           data-bs-toggle="dropdown">
                            {{ getCurrentMedium()->name }}
                        </a>
                        <div class="dropdown-menu">
                            @foreach (getMediums() as $medium)
                                <a class="dropdown-item"
                                   href="{{ route('medium.active', $medium->id) }}">{{ $medium->name }}</a>
                            @endforeach
                        </div>
                    </li>
                @endif
            @endif

            {{-- @endhasanyrole --}}

            {{-- @if (Auth::user()->teacher)
            <li class="nav-item dropdown language-drop me-2">
                <a href="#" class="dropdown-toggle nav-link header-nav-list active_center" data-bs-toggle="dropdown">
                    <img class="rounded-circle" src="{{ active_center('logo') }}" width="35" alt=""><span class="center_name">{{ active_center('name') }}</span>
                </a>
                <div class="dropdown-menu">
                    @foreach (get_teacher_center() as $center)
                        <a class="dropdown-item" href="{{ url('set-center',$center->center->id) }}"><img class="rounded-circle" width="25" src="{{ $center->center->logo }}" alt=""> <span class="center_name">{{ $center->center->name }}</span></a>
                    @endforeach
                </div>
            </li>
        @endif --}}

            <li class="nav-item dropdown me-2">

                <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                    @if (!empty(session()->get('language')) && session()->get('language') == 'fr')
                        <i class="flag flag-bl"></i>
                    @else
                        <i class="flag flag-lr"></i>
                    @endif
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ url('set-language') . '/en' }}"><i
                                class="flag flag-lr me-2"></i>English</a>
                    <a class="dropdown-item" href="{{ url('set-language') . '/fr' }}"><i
                                class="flag flag-bl me-2"></i>Francais</a>
                </div>
            </li>
            <!-- Notifications -->
        {{--            <li class="nav-item dropdown noti-dropdown me-2"> --}}
        {{--                <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown"> --}}
        {{--                    <img src="{{ URL::asset('assets/img/icons/header-icon-05.svg') }}" alt=""> --}}
        {{--                </a> --}}

        {{--                <div class="dropdown-menu notifications"> --}}
        {{--                    <div class="topnav-dropdown-header"> --}}
        {{--                        <span class="notification-title">Notifications</span> --}}
        {{--                        <a href="javascript:void(0)" class="clear-noti"> Clear All </a> --}}
        {{--                    </div> --}}
        {{--                    <div class="noti-content"> --}}
        {{--                        <ul class="notification-list"> --}}
        {{--                            <li class="notification-message"> --}}
        {{--                                <a href="#"> --}}
        {{--                                    <div class="media d-flex"> --}}
        {{--                                                    <span class="avatar avatar-sm flex-shrink-0"> --}}
        {{--                                                        <img class="avatar-img rounded-circle" alt="User Image" --}}
        {{--                                                             src="{{ URL::asset('assets/img/profiles/avatar-02.jpg') }}"> --}}
        {{--                                                    </span> --}}
        {{--                                        <div class="media-body flex-grow-1"> --}}
        {{--                                            <p class="noti-details"><span class="noti-title">Carlson Tech</span> has --}}
        {{--                                                approved <span class="noti-title">your estimate</span></p> --}}
        {{--                                            <p class="noti-time"><span class="notification-time">4 mins ago</span></p> --}}
        {{--                                        </div> --}}
        {{--                                    </div> --}}
        {{--                                </a> --}}
        {{--                            </li> --}}
        {{--                            <li class="notification-message"> --}}
        {{--                                <a href="#"> --}}
        {{--                                    <div class="media d-flex"> --}}
        {{--                                                    <span class="avatar avatar-sm flex-shrink-0"> --}}
        {{--                                                        <img class="avatar-img rounded-circle" alt="User Image" --}}
        {{--                                                             src="{{ URL::asset('assets/img/profiles/avatar-11.jpg') }}"> --}}
        {{--                                                    </span> --}}
        {{--                                        <div class="media-body flex-grow-1"> --}}
        {{--                                            <p class="noti-details"><span class="noti-title">International Software --}}
        {{--                                                                Inc</span> has sent you a invoice in the amount of <span --}}
        {{--                                                        class="noti-title">$218</span></p> --}}
        {{--                                            <p class="noti-time"><span class="notification-time">6 mins ago</span></p> --}}
        {{--                                        </div> --}}
        {{--                                    </div> --}}
        {{--                                </a> --}}
        {{--                            </li> --}}
        {{--                            <li class="notification-message"> --}}
        {{--                                <a href="#"> --}}
        {{--                                    <div class="media d-flex"> --}}
        {{--                                                    <span class="avatar avatar-sm flex-shrink-0"> --}}
        {{--                                                        <img class="avatar-img rounded-circle" alt="User Image" --}}
        {{--                                                             src="{{ URL::asset('assets/img/profiles/avatar-17.jpg') }}"> --}}
        {{--                                                    </span> --}}
        {{--                                        <div class="media-body flex-grow-1"> --}}
        {{--                                            <p class="noti-details"><span class="noti-title">John Hendry</span> sent a --}}
        {{--                                                cancellation request <span class="noti-title">Apple iPhone XR</span> --}}
        {{--                                            </p> --}}
        {{--                                            <p class="noti-time"><span class="notification-time">8 mins ago</span></p> --}}
        {{--                                        </div> --}}
        {{--                                    </div> --}}
        {{--                                </a> --}}
        {{--                            </li> --}}
        {{--                            <li class="notification-message"> --}}
        {{--                                <a href="#"> --}}
        {{--                                    <div class="media d-flex"> --}}
        {{--                                                    <span class="avatar avatar-sm flex-shrink-0"> --}}
        {{--                                                        <img class="avatar-img rounded-circle" alt="User Image" --}}
        {{--                                                             src="{{ URL::asset('assets/img/profiles/avatar-13.jpg') }}"> --}}
        {{--                                                    </span> --}}
        {{--                                        <div class="media-body flex-grow-1"> --}}
        {{--                                            <p class="noti-details"><span class="noti-title">Mercury Software --}}
        {{--                                                                Inc</span> --}}
        {{--                                                added a new product <span class="noti-title">Apple MacBook Pro</span> --}}
        {{--                                            </p> --}}
        {{--                                            <p class="noti-time"><span class="notification-time">12 mins ago</span> --}}
        {{--                                            </p> --}}
        {{--                                        </div> --}}
        {{--                                    </div> --}}
        {{--                                </a> --}}
        {{--                            </li> --}}
        {{--                        </ul> --}}
        {{--                    </div> --}}
        {{--                    <div class="topnav-dropdown-footer"> --}}
        {{--                        <a href="#">View all Notifications</a> --}}
        {{--                    </div> --}}
        {{--                </div> --}}
        {{--            </li> --}}
        <!-- /Notifications -->
        {{--            <li class="nav-item zoom-screen me-2"> --}}
        {{--                <a href="#" class="nav-link header-nav-list"> --}}
        {{--                    <img src="assets/img/icons/header-icon-04.svg" alt=""> --}}
        {{--                </a> --}}
        {{--            </li> --}}
        <!-- User Menu -->

            <li class="nav-item dropdown has-arrow new-user-menus">

                <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="{{ Auth::user()->image }}" width="31" alt="User"
                             onerror="laodDefaultLogo(this,'vertical_logo')" />
                        <div class=" user-text">
                            <h6>{{ Auth::user()->first_name }}</h6>
                            {{-- <p class="text-muted mb-0">{{ Auth::user()->roles->pluck('name')->first() }}</p> --}}
                            <p class="text-muted mb-0">{{ get_active_user_role() }}</p>
                        </div>
                    </span> </a>
                <div class="dropdown-menu">
                    <div class="user-header">
                        <div class="avatar avatar-sm">
                            <img src="{{ Auth::user()->image }}" alt="User Image" class="avatar-img rounded-circle"
                                 onerror="laodDefaultLogo(this,'vertical_logo')">
                        </div>
                        <div class="user-text">
                            <h6>{{ Auth::user()->first_name }}</h6>
                            <p class="text-muted mb-0">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ url('profile') }}">{{ __('My Profile') }}</a>
                    <a class="dropdown-item" href="{{ url('change-password') }}">{{ __('Change Password') }}</a>

                    {{-- @if (getCurrentMedium()->name)
                        @if (Auth::user()->center or Auth::user()->teacher or Auth::user()->staff()->first())
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">{{ getSessionName() }}</a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown dropend">
                                <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
                                    {{ getCurrentMedium()->name }}</a>
                                <div class="dropdown-menu">
                                    @foreach (getMediums() as $medium)
                                        <a class="dropdown-item"
                                            href="{{ route('medium.active', $medium->id) }}">{{ $medium->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif --}}
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">{{ __('Logout') }}</a>
                </div>
            </li>
            <!-- /User Menu -->

        </ul>
        <!-- /Header Right Menu -->

</div><!-- /Header -->
