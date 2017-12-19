<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
    <table class="table table-bordered">
        <thead class="thin-border-bottom">
        <tr class="">
            <th class="text-center w10 center">{{FunctionLib::viewLanguage('no')}}</th>
            <th class="center w50" width="w50">{{FunctionLib::viewLanguage('template_name')}}</th>
            <th class="center w200">{{FunctionLib::viewLanguage('content')}}</th>
            <th class="center w150">{{FunctionLib::viewLanguage('update')}}</th>
            <th class="center w100">{{FunctionLib::viewLanguage('action')}}</th>
        </tr>
        </thead>
        <tbody id="list_sms_template">
        @foreach ($data as $key => $item)
            <td class="text-center middle">{{$key+1 }}</td>
            <td>{{$item['template_name']}}</td>
            <td>{{ $item['content'] }}</td>
            <td>{{ $item['updated_date'] }}</td>
            <td class="center">
                <a onclick="edit_sms_template('{{FunctionLib::inputId($item['sms_template_id'])}}','{{$item['template_name']}}','{{$item['content']}}')"><i class="fa fa-pencil blue" aria-hidden="true"></i></a>
                <a onclick="delete_item('{{FunctionLib::inputId($item['sms_template_id'])}}')"><i class="fa fa-trash red" aria-hidden="true"></i></a>
            </td>
            </tr>
        @endforeach
        </tbody>
    </table>
