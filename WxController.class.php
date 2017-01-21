<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-01-21
* 版    本：1.0.0
* 功能说明：前台控制器演示。
*
**/
namespace Home\Controller;
use Think\Controller;
use Org\Util ;
use Think\Think;

class WxController extends ComController {




    public function index(){

 /* // 验证url信息  验证过之后即可注释掉
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr=$_GET['echostr'];
        $token = TOKEN;

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $echostr;
        }else{
            return false;
        }

        exit;*/
        /*
         *
         *
         *
         *  *   $type = $weObj->getRev()->getRevType();
 *   switch($type) {
 *   		case Wechat::MSGTYPE_TEXT:
 *   			$weObj->text("hello, I'm wechat")->reply();
 *   			exit;
 *   			break;
 *   		case Wechat::MSGTYPE_EVENT:
 *   			....
 *   			break;
 *   		case Wechat::MSGTYPE_IMAGE:
 *   			...
 *   			break;
 *   		default:
 *   			$weObj->text("help info")->reply();
 *   }
         *
         *
         *
         */
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
 		'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
 			'appid'=>C(APPID), //填写高级调用功能的app id
 		'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        		);
        //set_time_limit(0);
        $weObj=new Util\Wechatnew($options);
        //$weObj->valid();

        //$weObj = new Wechat($options);

       $type = $weObj->getRev()->getRevType();
        $zuozhe=$weObj->getRev()->getRevFrom();
       $t=$weObj->getRev()->getRevCtime();
        $md = md5($zuozhe.$t);
        $quchong = M('quchong');
        $re = $quchong->where(array('md'=>$md))->find();
        if($re){
            return "";
        }else{
            $quchong->data(array('md'=>$md))->add();
        }
          switch($type) {
           		        case $weObj::MSGTYPE_TEXT:
                  			$weObj->text("您好，详询4006409818，谢谢！")->reply();
                   			exit;
                   			break;
                   		case $weObj::MSGTYPE_EVENT:
                            //得到事件类型 是个数组key代表关键词 event代表事件类型 有click\subscribe类型
                            $event=$weObj->getRev()->getRevEvent();
                            if($event['event']== "subscribe" && empty($event['key'])){
                                $countuser=D('user');


                                $fharr = $countuser->where(array('openid'=>$zuozhe))->find();
                                if($fharr){

                                    $weObj->text('您的昵称是'.$fharr['nickname'].',于'.date("Y年m月d日 H时i分s秒",$fharr['addtime']).'成为第'.$fharr['huiyuanhao'].'位会员.您的上级会员是'.$fharr['shangjiname'].',因此不要重复关注,感谢您的支持')->reply();
                                    exit;
                                }else{
                                   $hyh = $this->saveuser($zuozhe);

                                    $weObj->text('欢迎关注,您是系统第'.$hyh.'位会员')->reply();
                                    exit;
                                }
                          

                                exit;
                                break;
                            }elseif($event['event']== "subscribe" && !empty($event['key']) ){
                                         $canshu = $weObj->getRevSceneId($event['key']);
                                $countuser=D('user');
                                $fharr = $countuser->where(array('openid'=>$zuozhe))->find();
                                if($fharr){
                                    $sjjarr = $countuser->where(array('openid'=>$canshu))->find();
                                    /*$sjjarr['openid'];
                                    $fharr['openid'];*/
                                    //$this->saveuser($zuozhe);
                                    $vvv = '您好,微信昵称为['.$sjjarr['nickname'].']的朋友,在'.date("Y-m-d H:i:s", $sjjarr['addtime']).'已经成为系统的会员,会员号为'.$sjjarr['huiyuanhao'].',因此不能重复关注,感谢您的支持';


                                    if($fharr['sid']==1){

                                       // $eee = $countuser->where(array('id'=>$fharr['sid']))->find();
                                        $weObj->text('您的昵称是'.$fharr['nickname'].',于'.date("Y年m月d日 H时i分s秒",$fharr['addtime']).'成为第'.$fharr['huiyuanhao'].'位会员,您的上级会员是'.$fharr['shangjiname'].',因此不要重复关注,感谢您的支持')->reply();

                                        $this->cuowu($canshu,$zuozhe);

                                        exit;
                                    }else{
                                        if($fharr['sid']==$sjjarr['id']){
                                            $weObj->text('您的昵称是'.$fharr['nickname'].',于'.date("Y年m月d日 H时i分s秒",$fharr['addtime']).'成为第'.$fharr['huiyuanhao'].'位会员,您的上级会员是'.$fharr['shangjiname'].',因此不要重复关注,感谢您的支持')->reply();
                                            exit;


                                        }else{
                                            $eee = $countuser->where(array('id'=>$fharr['sid']))->find();


                                            $weObj->text('您的昵称是'.$fharr['nickname'].',于'.date("Y年m月d日 H时i分s秒",$fharr['addtime']).'成为第'.$fharr['huiyuanhao'].'位会员,您的上级会员是'.$fharr['shangjiname'].',因此不要重复关注,感谢您的支持')->reply();
                                            $this->cuowu($canshu,$zuozhe);
                                            exit;
                                        }


                                    }

                                }else{

                                    $hyh = $this->saveuser($zuozhe);

                                    $this->shangji($zuozhe,$canshu);
                                    $this->se($canshu,$zuozhe,$hyh);
                                    $weObj->text('欢迎关注,您是系统第'.$hyh.'位会员')->reply();


                                    exit;
                                }

                              //  $this->se($canshu,$zuozhe);

                               /* $xiaoxi= new \Home\Controller\XiaoxiController();
                                $xiaoxi->addxiaoxi($zuozhe,'有人通过您的二维码关注公众号');
                                $weObj->text('欢迎您的关注,您是本系统的第'.$count.'位用户')->reply();*/
                            } elseif($event['key']=="erweima"){
                               // $weObj->text($event['key'].$zuozhe)->reply();
                               $jichu = new JichuController();
                               $chaxundata =  $this->panduanchongfu($zuozhe);
                                if($chaxundata['zhuangtai']==0){
                                    $xiaoxi= new \Home\Controller\XiaoxiController();
                                    $xiaoxi->addxiaoxi($zuozhe,'生成二维码');
                                    $weObj->text('您还未付款')->reply();
                                    exit;
                                }
                                if(!empty($chaxundata['erweima'])){
                                    $realurl = substr(dirname(__FILE__),0,14).$chaxundata['erweima'];//路径地址
                                    $imgdata["media"]='@'.$realurl;
                                    $type= "image";
                                    $fanhui = $weObj->uploadMedia($imgdata,$type);
                                    //$obj->image('media_id')->reply()
                                    $xiaoxi= new \Home\Controller\XiaoxiController();
                                    $xiaoxi->addxiaoxi($zuozhe,'生成二维码');
                                    $weObj->image($fanhui['media_id'])->reply();
                                  // $weObj->text($fanhui['media_id'])->reply();
                                }else{

                                    $imgurl =  $jichu->erweima($zuozhe);
                                    $this->ruku($imgurl,$zuozhe);
                                    $realurl = substr(dirname(__FILE__),0,14).substr($imgurl,1);//路径地址
                                    $imgdata["media"]='@'.$realurl;
                                    $type= "image";
                                    $fanhui = $weObj->uploadMedia($imgdata,$type);
                                    $xiaoxi= new \Home\Controller\XiaoxiController();
                                    $xiaoxi->addxiaoxi($zuozhe,'生成二维码');
                                    //$obj->image('media_id')->reply()
                                    $weObj->image($fanhui['media_id'])->reply();
                                }



                            }elseif($event['key']=="scan"){
                                $erzhi =  $weObj->getRevSceneId();
                                $weObj->text($erzhi)->reply();
                                exit;
                            }


                            exit;
                   			break;
                   		case $weObj::MSGTYPE_IMAGE:
                            $weObj->text($zuozhe)->reply();
                            exit;
                   			break;
                        case $weObj::EVENT_SCAN:
                           $erzhi =  $weObj->getRevSceneId();
                            $weObj->text($erzhi)->reply();

                            exit;
                            break;

                         default:
                   			$weObj->text("help info")->reply();
                   }





  /*     $neirong = $wechat->getRev()->getRevContent();//获取发布内容
        $event=$wechat->getRev()->getRevEvent();//获取事件
        $type=$wechat->getRev()->getRevType();//获取发布类型

        $zuozhe=$wechat->getRev()->getRevFrom();//发送者openid
        //$str= $wechat
      //  $wechat->text($str);
       // $wechat->text('您好'.$zuozhe.'您发的内容是'.$event['event'])->reply();//获取点击事件
         $wechat->text('您好'.$event['event'])->reply();//获取点击事件*/




    }
    public function ceshi(){
       // echo dirname(__FILE__).'<br>';
        //echo __PUBLIC__.'<br>';
        //echo substr(dirname(__FILE__),0,14);
    $user =D('user');
    echo $user->max('id');
    }
    public function cuowu($sjopenid,$zuozhe){

        //参数是二维码里面带的上级用户的openid
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        // $weObj->setTMIndustry(1);
        $user = D('user');
       $zuozhearr =  $user->where(array('openid'=>$zuozhe))->find();
        $f = '您好,微信昵称为['.$zuozhearr['nickname'].']的朋友,在'.date("Y-m-d H:i:s", $zuozhearr['addtime']).'已经成为系统的会员,会员号为'.$zuozhearr['huiyuanhao'].',因此不能重复关注,感谢您的支持';


        $data= array("touser" => $sjopenid,
            "template_id"=>'FjHssGIOIQP39C0P620PQrsGvAe7C-L7gNbF68U1Uaw',
            "url"=>C(URL).'/index.php/Home/Wx/user',
            "topcolor"=>"#7B68EE",
            'data'=>array('first'=>array('value'=>$f,
                'color'=>"#743A3A",
            ),
                'keyword1'=>array('value'=>'重复关注',
                    'color'=>"#743A3A",
                ),
                'keyword2'=>array('value'=>'已经有上级的不能重复关注',
                    'color'=>"#743A3A",
                ),
                'remark'=>array('value'=>"感谢您的支持",
                    'color'=>"#743A3A",
                )




            )



        );


            $weObj->sendTemplateMessage($data);




    }
    public function se($canshu,$zuozhe,$hyh){

      /*  ｛
        "touser":"OPENID",
			"template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
			"url":"http://weixin.qq.com/download",
			"topcolor":"#FF0000",
			"data":{
            "参数名1": {
                "value":"参数",
					"color":"#173177"	 //参数颜色
					},
				"Date":{
                "value":"06月07日 19时24分",
					"color":"#173177"
					},
				"CardNumber":{
                "value":"0426",
					"color":"#173177"
					},
				"Type":{
                "value":"消费",
					"color":"#173177"
					}
			}
		}
        */

            //参数是二维码里面带的上级用户的openid
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
       // $weObj->setTMIndustry(1);
        $sjarr = $weObj->getUserInfo($canshu);
        $zuozhearr = $weObj->getUserInfo($zuozhe);

        $data= array("touser" => $canshu,
                     "template_id"=>'mc7kDMEnlCpLdMTor4F3aDAb2wBjbjLe8nvQnY-JMJ4',
                     "url"=>C(URL).'/index.php/Home/Wx/user',
                     "topcolor"=>"#7B68EE",
                     'data'=>array('first'=>array('value'=>'您好,'.$sjarr['nickname'].','.$zuozhearr['nickname'].'会员号为['.$hyh.']刚刚通过二维码关注我们',
                                                   'color'=>"#743A3A",
                                                    ),
                                    'keyword1'=>array('value'=>$zuozhearr['nickname'],
                                                    'color'=>"#743A3A",
                                                    ),
                                     'keyword2'=>array('value'=>date("Y-m-d H:i:s",time()),
                                                     'color'=>"#743A3A",
                                                    ),
                                     'keyword3'=>array('value'=>$sjarr['nickname'],
                                                        'color'=>"#743A3A",
                                                     ),
                                     'remark'=>array('value'=>"您的推广卓有成效,请继续保持",
                                                        'color'=>"#743A3A",
                                                    )




                     )



        );


        //找到上级id
        $user =D('user');
        $sidarr = $user->where(array('openid'=>$zuozhe))->find();
        $sid = $sidarr['sid'];
        $i=0;

        while($sid >1 && $i<3) {

            $sidarra = $user->where(array('id' => $sid))->find();//找到上一级id

            $data = array("touser" => $sidarra['openid'],
                "template_id" => 'xtsMrZTi4L7oRg9WTDv50oCpcF4OKNE-bJ27VNPbcSY',
                "url" => C(URL) . '/index.php/Home/Wx/user',
                "topcolor" => "#7B68EE",
                'data' => array('first' => array('value' => '您好,' . $sidarra['nickname'] . ',' . $zuozhearr['nickname'] .'会员号为['.$hyh. ']刚刚通过二维码关注我们',
                    'color' => "#743A3A",
                ),
                    'keyword1' => array('value' => $zuozhearr['nickname'],
                        'color' => "#743A3A",
                    ),
                    'keyword2' => array('value' => date("Y-m-d H:i:s", time()),
                        'color' => "#743A3A",
                    ),
                    'keyword3' => array('value' => $sjarr['nickname'],
                        'color' => "#743A3A",
                    ),
                    'remark' => array('value' => "您的推广卓有成效,请继续保持",
                        'color' => "#743A3A",
                    )

                )


            );
            $weObj->sendTemplateMessage($data);

            $sid = $sidarra['sid'];
            $i++;
        }








    }
    /*
     *
     * @________用户入库
     * $param $zuozhe 传入用户的openid
     *
     *
     *
     * */
    public function saveuser($zuozhe){

        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $user= D('user');
        $weObj= new Util\Wechatnew($options);

            $mhuiyuanhao = $user->max('huiyuanhao');
        $ida =$user->data(array('huiyuanhao'=>$mhuiyuanhao+217))->add();



        //判断之前是否存过

            //判断之前是否存储过

            $userdata = $weObj->getUserInfo($zuozhe);//得到用户的信息   是个数组
            $userdata['addtime']= time();
            $userdata['id']=$ida;
        //用户昵称保存到MySQL中为空白
        $userdata['nickname'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $userdata['nickname']);
           // $userdata['huiyuanhao']=$mhuiyuanhao+17;
          //  $userdata['huiyuanhao']=$this->huiyuanhao();
            $user->save($userdata);
           $res =  $user->where(array('id'=>$ida))->find();
    return $res['huiyuanhao'];
          //  $weObj->text('您的昵称是'.$userdata['nickname'].'您于'.date("Y年m月d日 H时i分s秒",$userdata['addtime']).',成为本系统的第'.$userdata['huiyuanhao'].'位用户')->reply();




    }
    public function huiyuanhao(){
        $user =D('user');

    }
    public function myerweima(){
        $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/myerweima/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }else{

            $chaxunarr =  $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        if($_SESSION['userinfo']['zhuangtai']==1){
            //$this->redirect('Home/Anquan/erweima');





            if(!empty($chaxundata['erweima'])){
                //$realurl = substr(dirname(__FILE__),0,29).$chaxundata['erweima'];//路径地址
                //$imgdata["media"]='@'.$realurl;
                //$type= "image";
               // $fanhui = $weObj->uploadMedia($imgdata,$type);
                //$obj->image('media_id')->reply()

               // $xiaoxi= new \Home\Controller\XiaoxiController();
               // $xiaoxi->addxiaoxi($chaxundata['openid'],'生成二维码');
                $jichu = new JichuController();

                $imgurl =  $jichu->erweima($_SESSION['userinfo']['openid']);


                $this->ruku($imgurl,$_SESSION['userinfo']['openid']);
                //$weObj->image($fanhui['media_id'])->reply();
                // $weObj->text($fanhui['media_id'])->reply();
              //  $this->redirect('Home/Anquan/erweima');
                $this->redirect('Home/Anquan/erweima/openid/'.$_SESSION['userinfo']['openid']);
            } else{
               $jichu = new JichuController();

                $imgurl =  $jichu->erweima($_SESSION['userinfo']['openid']);


                $this->ruku($imgurl,$_SESSION['userinfo']['openid']);

              //  $xiaoxi= new \Home\Controller\XiaoxiController();
               // $xiaoxi->addxiaoxi($_SESSION['userinfo']['openid'],'生成二维码');

                $this->redirect('Home/Anquan/erweima/openid/'.$_SESSION['userinfo']['openid']);
            }






        }else{
            $this->redirect('Home/Chanpin/chanpin');
        }


    }
    public function denglu(){
        //var_dump($_POST);
        $username =$_POST['username'];
        $password =$_POST['password'];
        $user = D('user');

       /* $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $reurl = C(URL).U('Home/Wx/denglu/');
        if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);*/

        $idar = $user->where(array('openid'=>$_POST['openid'],'username'=>$username,'password'=>$password))->find();
        if($idar['id']){
            //$_SESSION['userinfo']=$idar;
            if($idar['zhuangtai']==1) {
                $this->redirect('Wx/user');
            }else{
                //T('Admin@Public/menu')
                $this->display(T('Home@Wx/tishi'));
                exit;

            }
        }else{
            $this->error('登录失败');

        }
    }
    public function tishi(){

        $this->display();
    }
    public function shangji($zuozhe,$canshu){
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $user= D('user');
        $weObj= new Util\Wechatnew($options);
        //通过$canshu找到上级ID号
        $shangjiarr = $user->where(array('openid'=>$canshu))->find();
        //找到个人信息id号
        $woarr = $user->where(array('openid'=>$zuozhe))->find();
        //存入数据库
        if($woarr['sid']== 1 && !($zuozhe == $canshu) && $woarr['id'] > $shangjiarr['id']) {
            $user->where(array('id' => $woarr['id']))->save(array('sid' => $shangjiarr['id'],'shangjiname'=>$shangjiarr['nickname']));

        }

    }
    public function ruku($imgurl,$openid){
        $user= D('user');
        $idarr = $user->where(array('openid'=>$openid))->find();
        //$user->data(array('erweima' => substr($imgurl,1),'openid'=>$openid))->add();
        $user->where(array('id'=>$idarr['id']))->save(array('erweima'=>substr($imgurl,1)));


    }
    public function panduanchongfu($zuozhe){
            $user = D('user');
         $chaxundata = $user->where(array('openid'=>$zuozhe))->find();
        if(!empty($chaxundata)){
            return $chaxundata ;
        }else{

            return false;
        }

    }
    public function zhuce(){
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $reurl = C(URL).U('Home/Wx/zhuce/');

        if(empty($_GET['code'])){
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
       // var_dump($a);
        //$weixinurl =  $weObj->getOauthRedirect($reurl);//获取到的是微信授权页面的url 需要去访问一下
        $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);
        $_SESSION['userinfo']=$userinfo;
        $user = D('user');
        $zarr =  $user->where(array('openid'=>$userinfo['openid']))->find();




           /* if ($zarr['zhuangtai'] == 0) {

                $this->assign('userinfo', $userinfo);

                $this->display();
            } elseif ($zarr['zhuangtai'] == 1) {}*/
                $_SESSION['userinfo'] = $zarr;
                $xiaoxi = new \Home\Controller\XiaoxiController();
                $xiaoxi->addxiaoxi($zarr['openid'],'登录');
                $this->redirect('Wx/user');






        //第一步提供一个跳转链接  给授权链接 然后返回一个code参数芳到跳转链接的后面  例如:redirect_uri/?code=CODE&state=STATE

        //第二步通过跳转了解换取token //这个token和基础支持的token不一样
        //第三步 刷新token (如果需要的话)
        //第四部拉取用户信息

    }
    public function tijiao(){
        $arr=$_POST;
        $t = array_keys(array_map('trim', $arr), '');
        if($t){
            $this->error('填写有误,请检查');

        }else{
           // echo '1111111';
            $zarr= $arr['openid'];
            $user = D('user');
           $chaxunarr =  $user->where(array('openid'=>$zarr))->find();
            $fid = $user->where(array('id' => $chaxunarr['id']))->save($arr);


            if($fid ){
                    if($fid['zhuangtai'] == 1) {
                        $_SESSION['userinfo']=$chaxunarr;
                        $this->redirect('Wx/user');

                    }else{

                        $this->display(T('Home@Wx/tishi'));
                        exit;

                    }
            }

        }
        exit;
    }
  /*  public function login(){
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $reurl = C(URL).U('Home/Wx/login/');

        if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        // var_dump($a);
        //$weixinurl =  $weObj->getOauthRedirect($reurl);//获取到的是微信授权页面的url 需要去访问一下
        $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);
        $this->assign('userinfo',$userinfo);

        //$this->display();

        $this->display();

    }*/
    /**
     *
     */
    public function user(){
           // $this->tijiao($_POST);


    $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/user/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }else{

            $chaxunarr =  $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $tixian= D('tixian');
        $dingdan = D('dingdan');
        $yongjincount = M('yongjinjilu')->where(array('userid'=>$_SESSION['userinfo']['id']))->count();
        $yijing = 0;//总计提现金额
        $dingdanshu = $dingdan->where(array('openid'=>$chaxunarr['openid']))->count();
        $jinbiarr = $tixian->where(array('openid'=>$_SESSION['userinfo']['openid']))->select();
        $xiaoxi = D('xiaoxi');
        $xiaoxicount = $xiaoxi->where(array('openid'=>$_SESSION['userinfo']['openid'],'status'=>0))->count();

      //  foreach ($jinbiarr as $key => $jinbia){
       //         $yijing += $jinbia['jiner'];

        //}
        $yongjinarr = M('yongjinjilu')->where(array('userid'=>$_SESSION['userinfo']['id']))->select();
        $allyongjin = 0;
        $yingyee= 0;
        foreach($yongjinarr as $key=>$yongjin){
            $allyongjin +=$yongjin['jiner'];//所有佣金
            $yingyee +=$yongjin['yingyee'];

        }



        //$mmm = $yijing+$chaxunarr['jinbi'];
        //已经付款的下级个数
        //得到所有下级arr

        $ct = count($jinbiarr);
        $xiajiidarr = $this->xiajiidarr($_SESSION['userinfo']['id']);//所有下级的列表
        if(is_array($xiajiidarr)) {
           /* $pp = array_unique($xiajiidarr);
            //$tgd =0;
            $tgdd = 0;
            foreach($pp as $xiajik){
                $tgd =$dingdan->where(array('sjid'=>array('in',$)))->select();

                $tgdd += count($tgd);
            }*/
            $tgdd = $dingdan->where(array('sjid' => array('in', $xiajiidarr)))->count();//所有订单数
        }else{
            $tgdd=0;
        }
        $yizhifuusercount=0;
        $weizhifuusercount=0;
        if(is_array($xiajiidarr)){
            $xiajicount = count($xiajiidarr);
            $where['id']=array('IN',$xiajiidarr);
            $where['zhuangtai']=1;
            $yizhifuusercount =  $user->where($where)->count();

            $wher['id']=array('IN',$xiajiidarr);
            $wher['zhuangtai']=0;
            $weizhifuusercount = $user->where($wher)->count();

        }else{
            $xiajicount=0;
        }




        $dizhi = D('dizhi');
        $dizhishu = $dizhi->where(array('openid'=>$_SESSION['userinfo']['openid']))->count();
        $_SESSION['userinfo']['mmm']=$allyongjin;

       // $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        //$fid = $user->where(array('id' => $chaxunarr['id']))->save($arr);

       // $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);
        //$_SESSION['userinfo'] = $chaxunarr;
        $this->assign('huiyuancount',$weizhifuusercount);
       // $this->assign('zfcount',$yizhifuusercount);
        $this->assign('dizhishu',$dizhishu);
        $this->assign('xjzfcount',$yizhifuusercount);
        $this->assign('ct',$ct);
        $this->assign('tgdd',$tgdd);
        $this->assign('yingyee',$yingyee);
        $this->assign('allyongjin',$allyongjin);
        $this->assign('yongjincount',$yongjincount);
        $this->assign('xjcount',$xiajicount);
        $this->assign('dingdanshu',$dingdanshu);
        $this->assign('yijing',$yijing);
        $this->assign('xiaoxicount',$xiaoxicount);

        $this->display();

    }
    public function caidan(){

        /*
         *
         *
         *
         *
         *
         *   * 	array (
     * 	    'button' => array (
     * 	      0 => array (
     * 	        'name' => '扫码',
     * 	        'sub_button' => array (
     * 	            0 => array (
     * 	              'type' => 'scancode_waitmsg',
     * 	              'name' => '扫码带提示',
     * 	              'key' => 'rselfmenu_0_0',
     * 	            ),
     * 	            1 => array (
     * 	              'type' => 'scancode_push',
     * 	              'name' => '扫码推事件',
     * 	              'key' => 'rselfmenu_0_1',
     * 	            ),
     * 	        ),
     * 	      ),
     * 	      1 => array (
     * 	        'name' => '发图',
     * 	        'sub_button' => array (
     * 	            0 => array (
     * 	              'type' => 'pic_sysphoto',
     * 	              'name' => '系统拍照发图',
     * 	              'key' => 'rselfmenu_1_0',
     * 	            ),
     * 	            1 => array (
     * 	              'type' => 'pic_photo_or_album',
     * 	              'name' => '拍照或者相册发图',
     * 	              'key' => 'rselfmenu_1_1',
     * 	            )
     * 	        ),
     * 	      ),
     * 	      2 => array (
     * 	        'type' => 'location_select',
     * 	        'name' => '发送位置',
     * 	        'key' => 'rselfmenu_2_0'
     * 	      ),
     * 	    ),
     * 	)
         *
         *
         *
         *
         *
         *
         *
         *
         *
         * */


        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
         $url = $weObj->getOauthRedirect(U('Home/Wx/zhuce'));

          $menu = $weObj->getMenu();
           //设置菜单
           $newmenu =  array(
    		"button"=>
    			array(
                    array ('type'=>'view','name'=>'商城首页','url'=>'http://djt.ruisi.me/index.php/Home/Shangcheng/index'),
    				/*array('type'=>'click','name'=>'生成二维码','key'=>'erweima'),*/

                    array ('type'=>'view','name'=>'名家荟萃','url'=>'http://djt.ruisi.me/index.php/Home/Weixin/shou'),
                  /*       'name' => '关于168众创',
      	                   'sub_button' => array (
      	                     0 => array (
      	                    'type' => 'view',
      	                    'name' => '模式介绍',
                        'url'=>'http://mp.weixin.qq.com/s?__biz=MzIyMzIzMjEwMA==&mid=403863476&idx=1&sn=55ff910923f43fd47a3568a3f2f8b492#rd'
      	            ),
      	            1 => array (
      	              'type' => 'view',
      	              'name' => '注册流程',
      	              'url' => 'http://mp.weixin.qq.com/s?__biz=MzIyMzIzMjEwMA==&mid=403871003&idx=1&sn=639c23a3cda57386b58790a4995637bf#rd',
      	            ),
                    2 => array (
                        'type' => 'view',
                        'name' => '注册要求',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzIyMzIzMjEwMA==&mid=403870805&idx=1&sn=afd6aba5e7ec885babfb38785eb7fe0c#rd',
                    ),
                    3 => array (
                        'type' => 'view',
                        'name' => '邀请好友方式',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzIyMzIzMjEwMA==&mid=403870686&idx=1&sn=cbd2fd411a99b87a6dfc7cd5bed1416c#rd',
                    ),
                    3 => array (
                        'type' => 'view',
                        'name' => '如何赚钱',
                        'url' => 'http://mp.weixin.qq.com/s?__biz=MzIyMzIzMjEwMA==&mid=403870711&idx=1&sn=0977ba03f290936fb02eedb777036722#rd',
                    ),
      	        ),*/


                    array(

                        'name'=>'自助服务',
                        'sub_button'=>array(

                            0=>array(
                                'type'=>'view',
                                'name'=>'预约挂号',
                                'url'=>'http://djt.ruisi.me/index.php/Home/Weixin/doctorList'

                            ),
                            // 1=>array(
                            //     'type'=>'view',
                            //     'name'=>'创业平台',
                            //     'url'=>'http://djt.ruisi.me/index.php/Home/chanpin/moshi'


                            // ),
                            // 2=>array(
                            //     'type'=>'view',
                            //     'name'=>'个人中心',
                            //     'url'=>'http://djt.ruisi.me/index.php/Home/Wx/user'

                            // )


                        )
                    ),

                    )
   		);
        $result = $weObj->createMenu($newmenu);




    }
    public function xiajigeshu(){
        $user =D('user');

        $id =$_SESSION['userinfo']['id'];
        $yiji = $user->where(array('sid'=>$id))->count();
        if(!$yiji){
            return 0;
            exit;
        };

        $yarr = $user->where(array('sid'=>$id))->select();

        foreach($yarr as $aaa){

            $erjiarr[] =$aaa['id'];

        }

        $erjirenshu =  $user->where(array('sid'=>array('IN',$erjiarr)))->count();//本质是二级的个数
        if(!$erjirenshu){
            return $yiji;

        }
        $sanjiarrbbb = $user->where(array('sid'=>array('IN',$erjiarr)))->select();//
        foreach($sanjiarrbbb as $sjarr){
            $sanjiarr[] = $sjarr['id'];
        }

        $sj =  $user->where(array('sid'=>array('IN',$sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数
        if(!$sj){
            return $yiji+$erjirenshu;
            exit;
        }
        return $sj+$erjirenshu+$yiji;



    }





    public function xiajiidarr($id){
        $user =D('user');


        /*if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        $_SESSION['userinfo'] = $chaxunarr;*/
        $yiji = $user->where(array('sid'=>$id))->count();
        if($yiji){
            $yarr = $user->where(array('sid'=>$id))->select();
            foreach($yarr as $aaa){

                $erjiarr[] =$aaa['id'];
                // $user->where(array('sid'=>$value['id']))->select();


            }


            $erjirenshu =  $user->where(array('sid'=>array('IN',$erjiarr)))->count();//本质是二级的个数
            if($erjirenshu){



                $sanjiarrbbb = $user->where(array('sid'=>array('IN',$erjiarr)))->select();//
                foreach($sanjiarrbbb as $sjarr){
                    $sanjiarr[] = $sjarr['id'];
                }


                $sj =  $user->where(array('sid'=>array('IN',$sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数
                        $sijiarrccc = $user->where(array('sid'=>array('IN',$sanjiarr)))->select();

                    if($sj){
                            foreach($sijiarrccc as $sjshuzu){
                                $sijiarr[] = $sjshuzu['id'];

                            }



                    }




            }else{


            }



        }else{




        }


        /*$erjicount=0;

        foreach($yarr as $value){ //循环出每个人的下级数进行相加
                $ercount= $user->where(array('sid'=>$value['id']))->count();

            $erjicount +=$ercount;

        }
        $erjiidarr=array();*/



       /* $_SESSION['sanji']=$sj;
        $_SESSION['erji']=$erjirenshu;
        $_SESSION['yiji']=$yiji;


        $this->display();*/
        //$xiajiidarr = array_merge($erjiarr,$sanjiarr,$sijiarr);

        if(is_array($erjiarr)){

            if(is_array($sanjiarr)){

                if(is_array($sijiarr)){
                    return array_merge($erjiarr,$sanjiarr,$sijiarr);
                }else{

                    return array_merge($erjiarr,$sanjiarr);
                }

            }else{
               return $erjiarr;
            }



        }else{
            return false;
        }






    }









    public function xiaji(){
        $user =D('user');
        $sj=0;
        $erjirenshu=0;
        $yiji=0;


        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        /*if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        $_SESSION['userinfo'] = $chaxunarr;*/

        $id =$_SESSION['userinfo']['id'];
        $yiji = $user->where(array('sid'=>$id))->count();
        if($yiji){
            $yarr = $user->where(array('sid'=>$id))->select();
            foreach($yarr as $aaa){

                $erjiarr[] =$aaa['id'];
                // $user->where(array('sid'=>$value['id']))->select();


            }

            $erjirenshu =  $user->where(array('sid'=>array('IN',$erjiarr)))->count();//本质是二级的个数
            if($erjirenshu){



                $sanjiarrbbb = $user->where(array('sid'=>array('IN',$erjiarr)))->select();//
                foreach($sanjiarrbbb as $sjarr){
                    $sanjiarr[] = $sjarr['id'];
                }

                $sj =  $user->where(array('sid'=>array('IN',$sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数






            }else{


            }



        }else{




        }


        /*$erjicount=0;

        foreach($yarr as $value){ //循环出每个人的下级数进行相加
                $ercount= $user->where(array('sid'=>$value['id']))->count();

            $erjicount +=$ercount;

        }
        $erjiidarr=array();*/



        $_SESSION['sanji']=$sj;
        $_SESSION['erji']=$erjirenshu;
        $_SESSION['yiji']=$yiji;


        $this->display();



    }
    public function yijihuiyuan(){
        $xiaoxi = new \Home\Controller\XiaoxiController();
        $xiaoxi->weixincheck();
        $id = $_SESSION['userinfo']['id'];
        $user=D('user');

        $yijiarr=  $user->where(array('sid'=>$id))->select();//查询到所有的一级会员id
        $this->assign('fanhuiarr',$yijiarr);

        $this->display();



    }
    public function erjihuiyuan(){

        $user =D('user');


        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $id = $_SESSION['userinfo']['id'];
        $user=D('user');
        $yijiarr=  $user->where(array('sid'=>$id))->select();//查询到所有的一级会员id
        foreach($yijiarr as $yijia){
                $erjiid[] = $yijia['id'];
        }
        $erjifanhui = $user->where(array('sid'=>array('IN',$erjiid)))->select();


        $this->assign('erjifanhui',$erjifanhui);

        $this->display();
    }
    function sanjihuiyuan(){

        $sj=0;
        $erjirenshu=0;
        $yiji=0;
        $user =D('user');


        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }



        $id =$_SESSION['userinfo']['id'];
        $yiji = $user->where(array('sid'=>$id))->count();
        if($yiji){


            $yarr = $user->where(array('sid'=>$id))->select();

            //$erjiidarr=array();
            foreach($yarr as $aaa) {

                $erjiarr[] = $aaa['id'];
            }

            $erjirenshu =  $user->where(array('sid'=>array('IN',$erjiarr)))->count();//本质是二级的个数
            if($erjirenshu){

                $sanjiarrbbb = $user->where(array('sid'=>array('IN',$erjiarr)))->select();//
                foreach($sanjiarrbbb as $sjarr){
                    $sanjiarr[] = $sjarr['id'];
                }
                $sj =  $user->where(array('sid'=>array('IN',$sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数

                if($sj){

                    $sjarr = $user->where(array('sid'=>array('IN',$sanjiarr)))->select();
                }


            }


        }






        $_SESSION['sanji']=$sj;
        $_SESSION['erji']=$erjirenshu;
        $_SESSION['yiji']=$yiji;
        $this->assign('sjarr',$sjarr);


        $this->display();






    }
    function yanzhengma(){

        $Verify = new \Think\Verify();


        $Verify->codeSet = '0123456789';
        $Verify->fontSize = 10;
        $Verify->length   = 3;
        $Verify->useCurve = false;
        $Verify->useNoise = false;


        $Verify->entry();

    }
    public function tixian2(){



    }
    public function tixian(){

      /* $this->error('提现模块正在优化,给您造成的不便,请谅解');
        exit;*/

        $tixian =D('tixian');
        $user = D('user');
        $id=$_GET['id'];
       $openidarr =  $user->where(array('id'=>$id))->field('openid')->find();
        //判断用户有咩有验证手机
      //  $shoujiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
        //var_dump(count($shoujiarr['tel']));
        //exit;
       // $b=strlen($_SESSION['userinfo']['tel']);

        if(strlen($_SESSION['userinfo']['tel'])<5){
            $this->redirect('Wx/tishix');
            exit;
        }


        $barr = $tixian->where(array('openid'=>$openidarr['openid']))->max('id');//找到用户最近一次提现记录的id值

        if(!isset($barr)){
            //$user = D('user');
            //$jinbiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->field('jinbi')->find();
            //$jinbiarr['jinbi']=$_GET['yongjin'];

            //找到可提现佣金

            $ketixiannew = new \Home\Controller\DingdanController();
            $ketixian = $ketixiannew->ketixian($id);
         //   $ketixianarr = $ketixiannew->ketixianarr($id);



            //


            $this->assign('ketixian',$ketixian);
            $this->display();

        }else{
            $carr = $tixian->where(array('id'=>(int)$barr))->find();//找到用户最近一次提现的所有记录
            $status = $carr['status'];//用户的提款状态 0代表正在提款中,1代表已经提款完毕

            if($status == 0){

                $this->display('dengdai');
                exit;
            }else{

                //判断用户有多少金币
               // $user = D('user');
               // $jinbiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
               // $jinbiarr['jinbi']=$_GET['yongjin'];

               //  $this->assign('jinbiarr',$jinbiarr);
               // $this->assign('dingdanhao',$_GET['dingdanhao']);
                //$this->display();
                //找到可提现佣金

                $ketixiannew = new \Home\Controller\DingdanController();
                $ketixian = $ketixiannew->ketixian($id);






                $this->assign('ketixian',$ketixian);
                $this->display();

            }

        }



    }
    public function dengdai(){
        $this->display();

    }
    public function sed($canshu,$zuozhe){

        $op=array(
            'account'=>'ruisisoft@163.com',
            'password'=>'ruisisoft'


        );
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $userinfo = $weObj->getUserInfo($zuozhe);
        $content= '用户'.$userinfo['nickname'].'通过你的二维码关注了公众号';

        $ko = new Util\Wechatext($op);
        $ko->send($canshu,$content);




    }
    public function jilu(){
        $openid = $_SESSION['userinfo']['openid'];
        $tixian = D('tixian');
        $fanhuiarr = $tixian ->where(array('openid'=>$openid)) ->select();


        $this->assign('fanhuiarr',$fanhuiarr);


        $this->display();

    }
    public function yongjinjilu(){
        $userid =$_GET['id'];
        //$userarr = M('user')->where(array('id'=>$userid))->field('nickname,headimgurl')->find();
        $yongjinarr = M('yongjinjilu')->where(array('userid'=>$userid))->join('qw_dingdan on qw_yongjinjilu.dingdanhao = qw_dingdan.dingdanhao')->order('qw_dingdan.zhifushijian desc')->select();
        $this->assign('yongjinarr',$yongjinarr);
        $this->display();

    }
    public function tishix(){
          /*  $yuanyin = $_GET['jishu'];
        $this->assign('yuanyin',$yuanyin);*/
        $this->display();

    }
    public function shoujiyemian(){
        $id= $_GET['id'];
        $user =D('user');
        $arr = $user->where(array('id'=>$id))->find();

        if(empty($arr['tel'])){
            $this->display();
        }else{
            $this->redirect('bdshouji');
            exit;
        }



    }
    public function duanxin(){
            return rand(100000,999999);

    }
    public function fasongyanzhengma(){
    $dianhua = $_GET['dianhua'];
        $duanxin = $this->duanxin();
        $dx = D('duanxin');

    $content = '【竹纺之家】验证码为:'.$duanxin.'(勿告诉他人)，请在页面中输入完成验证';


    $ch = curl_init();
    $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile='.$dianhua.'&content='.$content.'&tag=2';
    $header = array(
        'apikey: 66023c0fda2ad107941e49b5fd06c0bf',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

        $resn = json_encode(array('duanxin'=>$duanxin));

    echo $resn;


    }
    public function yanzhengsj(){

        $dh =$_GET['dh'];
        $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/yanzhengsj/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
       /* var_dump($_SESSION['userinfo']['id']);
        exit;*/
        $user->where('id='.$_SESSION['userinfo']['id'])->save(array('tel'=>$dh));

        $this->display('bdshouji');

    }
    public function bdshouji(){
        $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/bdshouji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        $this->display();
    }
    public function yanzhengx(){
        $this->display();
    }
    public function tixianxinxi(){

        $userid = $_GET['id'];

        $openidarr=M('user')->where(array('id'=>$userid))->field('openid')->find();
        $openid = $openidarr['openid'];
        $yongjin =D('yongjinjilu');

        $yongjinarr = $yongjin->where(array('userid'=>$userid))->select();
        $allyongjin = 0;
        $yingyee= 0;
        foreach($yongjinarr as $key=>$yongjin){
            $allyongjin +=$yongjin['jiner'];//所有佣金
            $yingyee +=$yongjin['yingyee'];

        }

        //未提现已经确认佣金

        $yiqueren = 0;
        $wherewww['zhifushijian']=array(array('lt',time()-86400),array('gt',0));
        $wherewww['tixianshenqing']=0;
        $wherewww['userid']=$userid;
        // $yongjinarr = $yongjin->where($wherewww)->select();


        $yiquerenarra = M('yongjinjilu')->where($wherewww)->select();
        
        foreach($yiquerenarra as $keya=>$yiquerena){
            $yiqueren +=$yiquerena['jiner'];//所有已确认佣金

        }
        //allyijing 所有已经确认
        $allyiqueren = 0;
        $wherewwwa['zhifushijian']=array(array('lt',time()-86400),array('gt',0));
        $wherewwwa['userid']=$userid;
        // $yongjinarr = $yongjin->where($wherewww)->select();

        $warr = M('yongjinjilu')->where($wherewwwa)->select();
        foreach($warr as $keyall=>$war){
            $allyiqueren +=$war['jiner'];//所有已确认佣金

        }




        //提现中金额
        $tixianzhonga = 0;
        if($tixianzhongarr = M('tixian')->where(array('openid'=>$openid,'status'=>0))->select()){
            foreach ($tixianzhongarr as $pp){
                $tixianzhonga += $pp['jiner'];//tixianzhong

            }
        }
        $yijinga = 0;
        if($yijingaarr = M('tixian')->where(array('openid'=>$openid,'status'=>1))->select()){
            foreach ($yijingaarr as $ppa){
                $yijinga += $ppa['jiner'];//yjingtixian

            }
        }







        //已提现佣金总额
        $this->assign('userid',$userid);
        $this->assign('tixianzhonga',$tixianzhonga);
        $this->assign('weiqueren',round($allyongjin-$allyiqueren,2));//未确认的金币
        $this->assign('yijing',$yijinga);
        $this->assign('ketixian',$yiqueren);
        $this->assign('yqrjb',$allyiqueren);//已经确认的金币
       // $this->assign('ktxa',$ktxa);
        $this->assign('mmm',$allyongjin);//所有推广佣金


        $this->display();


    }
    public function shijianshouhuo($dingdanhao){

        $user = D('user');
        $dingdan =D('dingdan');
        $dd = $dingdan->where(array('dingdanhao'=>$dingdanhao))->find();

        $dingdan->where(array('dingdanhao'=>$dingdanhao))->save(array('shouhuo'=>1,'shouhuoshijian'=>time()));

        //改状态 改成收货状态
        //找到上级的三人
        $ddarr = $dingdan->where(array('dingdanhao'=>$dingdanhao))->find();

        $id = $ddarr['sjid'];//找到下单id
        $benjiarr = $user->where(array('id'=>$id))->find();//找到上级id

        $yijiarr = $user->where(array('id'=>$benjiarr['sid']))->find();


        if($yijiarr && $yijiarr['id']>1){
            //找到上上一级id
            $yj = $ddarr['yijiyongjin'];
            //之前的 $yijiarr['jinbiquere']
            //存储订单号和金额
            $cunchun1= $yijiarr['jinbiquere'].'('.$dingdanhao.'-'.$yj.')';
            $user->where(array('id'=>$yijiarr['id']))->save(array('jinbiquere'=>$cunchun1));

            $erjiarr =$user->where(array('id'=>$yijiarr['sid']))->find();
            if($erjiarr){
                $ej = $ddarr['erjiyongjin'];
                $cunchun2= $erjiarr['jinbiquere'].'('.$dingdanhao.'-'.$ej.')';
                $user->where(array('id'=>$erjiarr['id']))->save(array('jinbiquere'=>$cunchun2));



                $sanjiarr = $user->where(array('id'=>$erjiarr['sid']))->find();
                if($sanjiarr){


                    $sj = $ddarr['sanjiyongjin'];
                    $cunchun3= $sanjiarr['jinbiquere'].'('.$dingdanhao.'-'.$sj.')';
                    $user->where(array('id'=>$sanjiarr['id']))->save(array('jinbiquere'=>$cunchun3));

                }
            }
        }






    }
    public function dotixian(){

        $xiaoxi = new \Home\Controller\XiaoxiController();

        $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj= new Util\Wechatnew($options);
            $reurl = C(URL).U('Home/Wx/dotixian/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        //先判断电话
        $tel = $_POST['tel'];
        $openid = $_POST['openid'];
        $tixian = D('tixian');
       // $tixian->where(array('openid'=>$openid))->max('id');
        $barr = $tixian->where(array('openid'=>$openid))->max('id');//找到用户最近一次提现记录的id值

        //$tixian->where()->
        if($barr){
            $carr = $tixian->where(array('id'=>(int)$barr))->find();//找到用户最近一次提现的所有记录
            $status = $carr['status'];//用户的提款状态 0代表正在提款中,1代表已经提款完毕
            if($status == 0){

                $this->display('dengdai');
                exit;
            }

        }



        $uarr =  $user->where(array('openid'=>$openid,'tel'=>$tel))->find();
        if(!$uarr){
            $this->redirect('yanzhengx');
            exit;

        }

        $_POST['addtime']=time();

        $tixian =D('tixian');
        if($_POST['jiner']>0){
            $ketixiannew = new \Home\Controller\DingdanController();
           $ketixian =  $ketixiannew->ketixian($_SESSION['userinfo']['id']);
            $ketixianarr=$ketixiannew->ketixianarr($_SESSION['userinfo']['id']);


            $_POST['jiner']=$ketixian;
            if($tixian->data($_POST)->add()){
                foreach($ketixianarr as $key =>$value){
                    M('yongjinjilu')->where(array('id'=>$value))->save(array('tixianshenqing'=>1));
                }

                $this->success('提现成功,请等待处理打款','user');
            };

        }else{
            $xiaoxi->addxiaoxi($_POST['openid'],'重复提现');
            $this->error('您没有金币或重复提交提现');
        }
      /*
       * 验证密码取消 更改为验证手机号
       *
       *
       *   $mimaarr =  $user->where(array('openid'=>$_POST['openid'],'password'=>$_POST['password']))->find();
        if(!is_array($mimaarr)){
        $this->error('密码错误');
        exit;
        }*/





    }




}
