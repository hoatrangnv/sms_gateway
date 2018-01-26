<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
<div class="table-responsive">
    <div class="span clearfix"> @if(count($dataSendClever) >0) Có tổng số <b>{{count($dataSendClever)}}</b> item @endif </div>
    <br>
    <table class="table table-bordered table-hover">
        <thead class="thin-border-bottom">
        <tr class="">
            <th width="5%" class="text-center">STT</th>
            <th width="15%"
                class="text-center">{{FunctionLib::viewLanguage('carrier')}}</th>
            <th width="15%"
                class="text-center">{{FunctionLib::viewLanguage('phone_number')}}</th>
            <th width="55%">{{FunctionLib::viewLanguage('sms_content')}}</th>
            <th width="10%"
                class="text-center">{{FunctionLib::viewLanguage('action')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($dataSendClever as $key => $item)
            <tr>
                <td class="text-center text-middle">{{$key+1}}</td>
                <td class="text-center text-middle">{{$item['carrier_name']}}</td>
                <td class="text-center text-middle">{{$item['phone_receive']}}</td>
                <td>{{$item['content']}}</td>
                <td class="text-center text-middle">

                    <a href="#"
                       onclick="SmsAdmin.getContentSmsClever('{{$item['sms_clever_id']}}')"
                       title="Sửa item"><i class="fa fa-edit fa-2x"></i></a>

                    <span class="img_loading" id="img_loading_{{$item['sms_clever_id']}}"></span>
                    <a href="#"
                       onclick="SmsAdmin.removeSmsClever('{{FunctionLib::inputId($item['sms_clever_id'])}}')"
                       title="Xóa item"><i class="fa fa-trash fa-2x"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>