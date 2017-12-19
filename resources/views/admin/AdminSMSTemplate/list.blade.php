<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
    <table class="table table-bordered">
        <thead class="thin-border-bottom">
        <tr class="">
            <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
            <th width="w50">{{FunctionLib::viewLanguage('template_name')}}</th>
            <th width="w100">{{FunctionLib::viewLanguage('content')}}</th>
            <th width="w100">{{FunctionLib::viewLanguage('update')}}</th>
            <th width="w100">{{FunctionLib::viewLanguage('action')}}</th>
        </tr>
        </thead>
        <tbody id="list_sms_template">
        @foreach ($data as $key => $item)
            <td class="text-center middle">{{$key+1 }}</td>
            <td>{{$item['template_name']}}</td>
            <td>{{ $item['content'] }}</td>
            <td>{{ $item['updated_date'] }}</td>
            <td>thao tac</td>
            </tr>
        @endforeach
        </tbody>
    </table>
