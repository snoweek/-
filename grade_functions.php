<?php
    define('SAE_MYSQL_USER','');//用户名　 :  SAE_MYSQL_USER
    define('SAE_MYSQL_PASS','');//密　　码 :  SAE_MYSQL_PASS
    define('SAE_MYSQL_HOST_M','');//主库域名 :  SAE_MYSQL_HOST_M
    define('SAE_MYSQL_HOST_S','');//从库域名 :  SAE_MYSQL_HOST_S
    define('SAE_MYSQL_PORT','');//端　　口 :  SAE_MYSQL_PORT
    define('SAE_MYSQL_DB','k');//数据库名 :  SAE_MYSQL_DB

    function connect_mysql(){  
        mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        mysql_select_db(SAE_MYSQL_DB, $db);
    }

     

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


    function insert_user($submit_open_id,$submit_student_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="insert into user(open_id,student_id)values('$submit_open_id','$submit_student_id')";
        $r=mysql_query($q);
        $rows=mysql_affected_rows();
        return $rows;    
    }

    function delete_user($submit_open_id){
        $db=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
        $dbc=mysql_select_db(SAE_MYSQL_DB, $db);
        $q="delete from user where open_id='$submit_open_id'";
        $r=mysql_query($q);
        $rows=mysql_affected_rows();
        return $rows;    
    }


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
?>