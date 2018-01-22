<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
<table class="table table-bordered">
    <thead class="thin-border-bottom">
    <tr class="">
        <th class="text-center center" width="5%">{{FunctionLib::viewLanguage('no')}}</th>
        <th class="center" width="20%">{{FunctionLib::viewLanguage('template_name')}}</th>
        <th class="center" width="70%">{{FunctionLib::viewLanguage('content')}}</th>
        <th class="center" width="5%">{{FunctionLib::viewLanguage('action')}}</th>
    </tr>
    </thead>
    <tbody id="list_sms_template">
    @foreach ($data as $key => $item)
        <td class="text-center middle">{{$key+1 }}</td>
        <td>{{$item['template_name']}}</td>
        <td>{{ $item['content'] }}</td>
        <td class="center">
            <a href="#" onclick="Admin.chooseSmsTemp('{{$item['content']}}')" title="Choose template sms"><i class="fa fa-thumbs-o-up fa-2x" aria-hidden="true"></i></a>
        </td>
        </tr>
    @endforeach
    </tbody>
</table>
