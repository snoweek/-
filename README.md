# 微信公众号接口开发--snoweek测试
snoweek测试是一个基于php开发的微信公众号，最初的目标是做成一个校园公众号，可以完成以下功能 
* 查询学生成绩：回复成绩，会返回一个包含成绩结果的链接 
* 查询城市天气：回复天气加城市，会返回当前天气实况，相关天气指数，未来几天天气预报
* 查询当前时间：回复时间，则返回当前时间 
* 发送一张图片，识别后返回信息"你发送了一张图片" 
* 回复天气，返回"请按提示输入【天气查询】请输入天气加城市;如：天气北京"
* 回复此公众号还未实现的功能，则会返回"抱歉，还未提供此功能。"


##项目结构
* index.php:微信接口文件
* weather.php:天气预报接口文件
* grade_functions.php:学生查询成绩时，需要用到的相关函数


##数据库user表信息
* user
```
CREATE TABLE  `user` (
  `user_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(30) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

```
表user包含三列，user_id记录用户绑定学号的顺序，同时用作主键;open_id记录用户在微信中帐号;student_id记录学生的学号。
* grade_list

```
CREATE TABLE `grade_list` (
  `grade_id` mediumint(9) NOT NULL,
  `course` text NOT NULL,
  `grade` int(11) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
表grade_list包含四列，grade_id用于记录成绩的条数;course用于记录课程名称;
grade记录成绩;student_id记录学生的学号。两个表之间以student_id来连接。

##功能详细介绍

###查询学生成绩
1. 若用户还未绑定学号，则返回提示信息："对不起，你还没有绑定学号，请输入成绩加学好;如：成绩199434040086"。

check_user()函数
```
function check_user($submit_open_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="select student_id from user where open_id='$submit_open_id'";
        $r=mysql_query($q);
        if(mysql_num_rows($r)==1){
             while($user=mysql_fetch_array($r)){
                $result=$user['student_id'];
            }    
        }else{
            $result=0;
        }
        return $result;   
    }
```
此函数通过发送者帐号$open_id在user表中进行查询。

2. 若用户已绑定学号，但grade_list表中没有其学生信息，则返回提示信息："你没有成绩记录"。

3. 若用户已绑定学号，且grade_list表中有其成绩信息，则返回一个链接，学生通过点击该链接查询自己的成绩。

search_grade()
```

    function search_grade($submit_student_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="select course,grade from grade_list where student_id='$submit_student_id'";
        $r=mysql_query($q);
        $grade_list=array();
        if(mysql_num_rows($r)!=0){
             while($g=mysql_fetch_array($r)){
                $grade=array();
                $grade['course']=$g['course'];
                $grade['grade']=$g['grade'];
                $grade_list[]=$grade;                 
            }
            $result=$grade_list;
        }else{
            $result=0;
        }
        return $result;           
    }
```

4. 若用户进行绑定学号行为，即输入成绩加学号

```
function insert_user($submit_open_id,$submit_student_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="insert into user(open_id,student_id)values('$submit_open_id','$submit_student_id')";
        $r=mysql_query($q);
        $rows=mysql_affected_rows();
        return $rows;    
    }
```

5. 用户可以对学号进行解绑，即输入“解绑即可”

```
function delete_user($submit_open_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="delete from user where open_id='$submit_open_id'";
        $r=mysql_query($q);
        $rows=mysql_affected_rows();
        return $rows;    
    }
```

### 查询城市天气：回复天气加城市，会返回当前天气实况，相关天气指数，未来几天天气预报
直接在聚合函数里找的天气接口，weather.php里包含多种查询天气的函数。
调用天气接口的代码如下：
```
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
```

### 查询当前时间
```
$msgType = "text";
$contentStr = date("Y-m-d H:i:s",time());
$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
echo $resultStr;
```
### 发送一张图片
```
if($msgtype=="image"){
  $msgType = "text";
  $contentStr = "你发送了一张图片";
  $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
  echo $resultStr;
}
```

### 回复此公众号还未实现的功能
```
$msgType = "text";
$contentStr = "【".$keyword."】抱歉，还未提供此功能。";
$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
echo $resultStr; 
```



##License
Apache 









