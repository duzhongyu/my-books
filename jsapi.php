<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $_POST['openid'];
$openId = $_GET['openid'];

$dingdanhao = $_GET['dingdanhao'];
$no =$_GET['dingdanhao'];
$total = $_GET['total'];

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("菜园网支付");
$input->SetAttach("菜园网官方店");
$input->SetOut_trade_no($no);
//$input->SetTotal_fee($_POST['total']*1000);

//$input->SetTotal_fee($_GET['total'] * 80);

$input->SetTotal_fee($_GET['total'] * 100);

$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 6000));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://caiyuanwang.ruisi.me/zhifu/example/ceshi2.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
echo '<font color="#f00"><br>支付单信息</br></font><br/>';
echo '<input type="hidden" name="dingdanhao" id="dingdanhao" value="'.$_GET['dingdanhao'].'"/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付</title>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                    if(res.err_msg == 'get_brand_wcpay_request:ok'){
                       // alert('支付成功');
                        var a=document.getElementById("dingdanhao").value;
                        window.location.href="http://caiyuanwang.ruisi.me/index.php/Home/Dingdan/tishi";
                    }else{
                        alert('请重新购买!');
                    }
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
    <script type="text/javascript">
        //获取共享地址
        function editAddress()
        {
            WeixinJSBridge.invoke(
                'editAddress',
                <?php echo $editAddress; ?>,
                function(res){
                    var value1 = res.proviceFirstStageName;
                    var value2 = res.addressCitySecondStageName;
                    var value3 = res.addressCountiesThirdStageName;
                    var value4 = res.addressDetailInfo;
                    var tel = res.telNumber;

                    alert(value1 + value2 + value3 + value4 + ":" + tel);
                }
            );
        }

       /* window.onload = function(){
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', editAddress);
                    document.attachEvent('onWeixinJSBridgeReady', editAddress);
                }
            }else{
                editAddress();
            }
        };*/

    </script>
</head>
<body>
<br/>
<font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px"><?php echo $_GET['total']?></span>元</b></font><br/><br/>
<font color="#9ACD32"><b><span style="color: red">注意:付款后点击完成,请耐心等待几秒,系统会自动跳转,勿退出或返回</span></b></font><br/><br/>

<div align="center">
    <button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
</div>
</body>
</html>