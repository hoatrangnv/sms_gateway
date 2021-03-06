<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
    <table class="table table-bordered">
        <thead class="thin-border-bottom">
        <tr class="">
            <th class="text-center center" style="width: 10%; font-weight: normal!important;">{{FunctionLib::viewLanguage('no')}}</th>
            <th class="center" style="width: 20%; font-weight: normal!important;">{{FunctionLib::viewLanguage('template_name')}}</th>
            <th class="center" style="width: 30%; font-weight: normal!important;">{{FunctionLib::viewLanguage('content')}}</th>
            <th class="center" style="width: 20%; font-weight: normal!important;">{{FunctionLib::viewLanguage('update')}}</th>
            <th class="center" style="width: 20%; font-weight: normal!important;">{{FunctionLib::viewLanguage('action')}}</th>
        </tr>
        </thead>
        <tbody id="list_sms_template">
        @foreach ($data as $key => $item)
            <td class="text-center middle">{{$key+1 }}</td>
            <td>{{$item['template_name']}}</td>
            <td>{{ $item['content'] }}</td>
            <td>{{ $item['updated_date'] }}</td>
            <td class="center">
                <a class="btn btn-primary" onclick="edit_sms_template('{{FunctionLib::inputId($item['sms_template_id'])}}','{{$item['template_name']}}','{{$item['content']}}')"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                <a class="btn btn-danger" onclick="delete_item('{{FunctionLib::inputId($item['sms_template_id'])}}')"><i class="fa fa-trash" aria-hidden="true"></i></a>
            </td>
            </tr>
        @endforeach
        </tbody>
    </table>
