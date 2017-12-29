<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
    <table class="table table-bordered">
        <thead class="thin-border-bottom">
        <tr class="">
            <th class="text-center w10 center">{{FunctionLib::viewLanguage('no')}}</th>
            <th class="center w50" width="w50">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_name')}}</th>
            <th class="center w200">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('description')}}</th>
            <th class="center w100">{{FunctionLib::viewLanguage('action')}}</th>
        </tr>
        </thead>
        <tbody id="list_app">
        @foreach ($data as $key => $item)
            <td class="text-center middle">{{$key+1 }}</td>
            <td>
                <a href="#" class="mg-t20" onclick="showDetails('{{$item['app_name']}}','{{$item['client_id']}}','{{$item['client_secret']}}')" data-toggle="modal" data-target="#modal-app-details">
                    {{$item['app_name']}}
                </a>
            </td>
            <td>{{ $item['description'] }}</td>
            <td class="center">
                <a onclick="edit_app('{{FunctionLib::inputId($item['app_id'])}}','{{$item['app_name']}}','{{$item['description']}}')"><i class="fa fa-pencil blue" aria-hidden="true"></i></a>
                <a onclick="delete_item('{{FunctionLib::inputId($item['app_id'])}}')"><i class="fa fa-trash red" aria-hidden="true"></i></a>
            </td>
            </tr>
        @endforeach
        </tbody>
    </table>
