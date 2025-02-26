@php
    $toolbar = 'data-toolbar=#toolbar';
    if(!empty($attributes->get('toolbar'))){
        $toolbar = ($attributes->get('toolbar')=="false") ? '' : 'data-toolbar="'.$attributes->get('toolbar').'"';
    }
@endphp

<table aria-describedby="mydesc" class='table table-striped table_list'
       id='{{$attributes->get('table_id')??"table_list"}}'
       data-toggle="table" data-url="{{$attributes->get('url')}}" data-click-to-select="true"
       data-side-pagination="server"
       data-maintain-meta-data="true"
       data-page-list="{{$attributes->get('pageList')??"[5, 10, 20, 50, 100, 200,All]"}}"
       data-search="{{ $attributes->get('data_search')??true }}"
       {{$toolbar}} data-show-columns="{{ $attributes->get('table_column')??true }}"
       data-show-refresh="{{ $attributes->get('table_refresh')??true }}" data-fixed-columns="true"
       data-fixed-number="2" data-fixed-right-number="1"
       data-trim-on-search="false" data-mobile-responsive="true"
       data-sort-name="{{$attributes->get('sortName')??"id"}}"
       data-sort-order="{{$attributes->get('sortOrder')??"DESC"}}"
       data-maintain-selected="true" data-export-types='["txt","excel", "pdf"]'
       data-export-options='{ "fileName": "list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
       data-show-export="{{ $attributes->get('table_export')??true }}"
       data-query-params="{{$attributes->get('queryParams')??'generalQueryParams'}}"
       data-use-row-attr-func="{{$attributes->get('draggableRows')??"false"}}"
       data-reorderable-rows="{{$attributes->get('draggableRows')??"false"}}"
       data-pagination="{{$attributes->get('showPagination')??"true"}}"
       data-response-handler="{{$attributes->get('data_response_handler')??""}}">
    <thead>
    @php
        $columns = $attributes->get('columns');

        //Generate Action Buttons here
        $actionColumn = $attributes->get('actionColumn')??true;
        $showActionColumn = $actionColumn??false;
        if($showActionColumn || is_array($showActionColumn)){
            $columns[__('Actions')] = array(
                "data-formatter"=>"actionColumnFormatter",
                'data-field'=>'operate',
                'data-events'=>$actionColumn['data-events']??null,
                'data-buttons'=>json_encode([
                    'customButton'=>$actionColumn['customButton']??false,
                  'viewButton'=>$actionColumn['viewButton']??true, // Ensure
                    'editButton'=>$actionColumn['editButton']??false,
                    'customModal'=>$actionColumn['customModal']??false,
                    'deleteButton'=>$actionColumn['deleteButton']??false,
                ])
            );
        }
    @endphp

    <tr>
        @foreach($columns as $key=>$row)
            <th scope="col" @foreach($row as $paramKey=>$paramValue){{$paramKey.'='}}"{{$paramValue}}"@endforeach >{{$key}}</th>
        @endforeach
    </tr>
    </thead>
</table>
