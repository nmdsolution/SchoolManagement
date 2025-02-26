@extends('layout.master')

@section('title')
    {{ __('role_management') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('role_management') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="mx-auto mb-3">
                                @if (Auth::user()->can('role-create'))
                                <a href="{{ url('roles/create') }}" class="btn btn-sm btn-primary float-end"><i
                                    class="feather-plus"></i>{{ __('ADD') }}</a>    
                                @endif

                            </div>
                        </div>


                        @php
                            $url = url('role_list');
                            $data_search = false;
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('name') => ['data-field' => 'name', 'data-sortable' => true, 'data-visible' => true],
                            ];
                            $actionColumn = [
                                'editButton' => false,
                                'deleteButton' => false,
                                'data-events' => 'roleEvents',
                                'customButton'=>[
                                    
                                    ['iconClass'=>'feather-eye','url'=>url('user/role/show'),'title'=>'Show Role','customClass'=>'edit-class-subject'],
                                    ['iconClass'=>'feather-edit','url'=>url('user/role/edit'),'title'=>'Edit Role','customClass'=>'edit-class-subject'],

                                    ['iconClass'=>'feather-trash','url'=>url('role/delete'),'title'=>'Delete Role','customClass'=>'edit-class-subject'],
                                    // ['iconClass'=>'feather-edit','url'=>url('user/role/edit'),'title'=>'Edit Role','customClass'=>'edit-class-subject'],
                                    // ['iconClass'=>'feather-trash','url'=>url('role/delete'),'title'=>'Delete Role','customClass'=>'edit-class-subject']
                                        
                                ],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :data_search=$data_search :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>

                        {{-- <table class="table table-bordered">
                            <tr>
                                <th>{{__('no')}}</th>
                                <th>{{__('name')}}</th>
                                <th width="280px">{{__('action')}}</th>
                            </tr>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>1</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <a class="btn btn-sm bg-success-light role-action btn-icon" href="{{ route('roles.show',$role->id) }}"><i class="feather-eye"></i></a>
                                        @can('role-edit')
                                            @if ($role->is_default == 0)
                                                <a class="btn btn-sm bg-success-light role-action btn-icon" href="{{ route('roles.edit',$role->id) }}"><i class="feather-edit"></i></a>
                                            @endif
                                        @endcan
                                        @can('role-delete')
                                            @if ($role->is_default == 0)
                                                <a class="btn btn-sm bg-danger-light role-action btn-icon delete-form" href="{{ route('roles.destroy',$role->id) }}"><i class="feather-trash"></i></a>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </table> --}}


                        {{-- {!! $roles->render() !!} --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
