<?php
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      

        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $event=$postObj->Event;
            $msgtype=$postObj->MsgType;
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if($event=="subscribe"){
                 $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                            <item>
                                <Title><![CDATA[欢迎关注snoweek测试]]></Title>
                                <Description><![CDATA[点击进入我的博客]]></Description>
                                <PicUrl><![CDATA[http://1.snoweek.applinzi.com/picture/1.jpeg]]></PicUrl>
                                <Url><![CDATA[http://snoweek.github.io]]></Url>
                            </item>
                        </Articles>
                        </xml>";
                $msgType = "text";
                $contentStr = "欢迎关注snoweek测试";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                echo $resultStr;
            }
            if($msgtype=="image"){
                $msgType = "text";
                $contentStr = "你发送了一张图片";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }
                        
            if($keyword == "时间"){
                $msgType = "text";
                $contentStr = date("Y-m-d H:i:s",time());
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else if( mb_substr($keyword,0,2,'utf-8')=="天气"&&strlen($keyword)==6){
                $msgType = "text";
                $contentStr = "请按提示输入【天气查询】请输入天气加城市;如：天气北京";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;                
            }else if( mb_substr($keyword,0,2,'utf-8')=="天气"&&strlen($keyword)>6){              
                include 'weather.php'; //引入天气请求类
                $appkey = 'f67769dc51bfad1c06bb09312b873176'; //您申请的天气查询appkey
                $weather = new weather($appkey);
                $cityname=mb_substr($keyword,2,5,'utf-8');
                $cityWeatherResult = $weather->getWeather($cityname);                
               if($cityWeatherResult['error_code'] == 0){ 
                    $data = $cityWeatherResult['result'];
                    $msgType = "text";
                    $contentStr = "==当前天气实况==\n温度：".$data['sk']['temp']."\n"."风向：".$data['sk']['wind_direction']."（".$data['sk']['wind_strength']."）"."\n湿度：".$data['sk']['humidity'];
                   	$contentStr.="\n\n==相关天气指数=======\n"."穿衣指数：".$data['today']['dressing_index']." , ".$data['today']['dressing_advice'];
                   	$contentStr.="\n\n==未来几天天气预报==\n";
                   	foreach($data['future'] as $wkey =>$f){
       
                        $contentStr.="日期:".$f['date']." ".$f['week']." ".$f['weather']." ".$f['temperature']."\n";
                    }
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
               }else{
                    $msgType = "text";
                    $contentStr = "【".$cityname."】".$cityWeatherResult['reason']."，请确保城市信息输入有效";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;                  
               }                              
            }else if($keyword == "成绩"){
                include 'grade_functions.php';
                $r=check_user($fromUsername);
                if($r==0){
                    $msgType = "text";
                    $contentStr = "对不起，你还没有绑定学号，请输入成绩加学好;如：成绩199434040086";  
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr; 
                }else{
                    $r2=check_grade($r);
                    if($r2==0){
                        $msgType = "text";
                        $contentStr = "你没有成绩记录";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }else{
                        $msgType = "text";
                        $contentStr = "<a href=\"http://1.snoweek.applinzi.com/grade.php?student_id=$r\">成绩请戳这里</a>";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }
                    
                }                
            }else if(mb_substr($keyword,0,2,'utf-8')=="成绩"&&strlen($keyword)>6){
                include 'grade_functions.php';
                $student_id=mb_substr($keyword,2,12,'utf-8');             
                $r=insert_user($fromUsername,$student_id);
                if($r==1){
                    $msgType = "text";
                    $contentStr = "学号绑定成功，请输入【成绩】，进行成绩查询";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr; 
                }else{
                    
                    $msgType = "text";
                    $contentStr = "对不起，学号绑定失败，请输入成绩加学好;如：成绩199434040086，重新进行绑定啊";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr; 
                }           	
            }else if($keyword == "解绑"){ 
                 include 'grade_functions.php';
                $r=delete_user($fromUsername);
                if($r==1){
                     $contentStr = "解绑成功";                     
                }else{
                  $contentStr = "解绑失败";                    
                }               
                $msgType = "text";     
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr; 
            }else{
                $msgType = "text";
                $contentStr = "【".$keyword."】抱歉，还未提供此功能。";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;                 
            }                
        }else{
            echo "";
            exit;
        }       
    }
}
?>