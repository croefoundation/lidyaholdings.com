<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외

//******************************************************************************************
//회원실적 계산 루틴
//2017.08.05
//
//******************************************************************************************

//먼저회원등록 테이블에서 :  각자 전체실적을 일괄 삭제를 한다......
$query= "select * from member where id='mem' order by id asc";
mysql_query("set names ust8", $connect);
$result= mysql_query($query, $connect);
while($data = mysql_fetch_array($result)){ //while문 시작 계약서를 하나둘씩 불러와서..수당테이블에 저장한다.--------------------------------

//전체 삭제하기 --------------------------------
                  $total_money=0;
                         //매출 <회원 정보>업데이트//////////////////////////////////////
                         $query="update member set
                         total_money='$total_money',
                         total_month='$total_month'

                         where user_id='$data[user_id]' ";
                         mysql_query("set names utf8", $connect);
                         mysql_query($query, $connect);
                         mysql_close;
//-----------------------------------------------------------
     }  //삭제완료

//삭제여부 확인
echo "회원정보 삭제 결과확인 <br><br>";
     $query= "select * from member where id='mem' order by id asc";
     mysql_query("set names ust8", $connect);
     $result= mysql_query($query, $connect);
     while($data = mysql_fetch_array($result)){
     echo "성명 :(".$data[name].")".number_format($data[total_money])." <br>";
      }


//계약테이블을 조회해서 우선 불러온다..-----------
//    1.약정기간이 먼저인 순서로 소팅한다--본부장 정하게여..

$query= "select * from contract where id='ctr' order by ctr_start asc";
mysql_query("set names ust8", $connect);
$result= mysql_query($query, $connect);
while($data = mysql_fetch_array($result)){ //while문 시작 계약서를 하나둘씩 불러와서..수당테이블에 저장한다.--------------------------------

$id= $data[id];
$newtype=$data[newtype];
$ctr_no= $data[ctr_no];
$name= $data[name];
$user_id=$data[user_id]; //유저아이디
$id_manager=$data[id_manager];
$id_incentive=$data[id_incentive];
$id_top=$data[id_top];

$type= $data[type];
$money= $data[money];
$money_old= $data[money_old];
$money_new= $data[money_new];
$ctr_start=$data[ctr_start];
$ctr_end=$data[ctr_end];
$ctr_date=$data[ctr_date];

$rate_cus= $data[rate_cus];

$rate_manager= $data[rate_manager];
$rate_incentive= $data[rate_incentive];
$rate_top= $data[rate_top];

echo $ctr_start."<br>";




//1. 고객 매출 입력
$query_m ="select * from member where user_id='$user_id'";  //회원테이블 불러서
$result_m=mysql_query($query_m,$connect);
$data_m = mysql_fetch_array($result_m);

if($type!="월대차/만기지급"){
$total_money=$data_m[total_money]+$money; }
else{ $total_month=$data_m[total_month]+$money; }

     //매출 <회원 정보>업데이트//////////////////////////////////////
     $query="update member set
     total_money='$total_money',
     total_month='$total_month'

     where user_id='$data_m[user_id]' ";
     mysql_query("set names utf8", $connect);
     mysql_query($query, $connect);
     mysql_close;

     echo "고객 : 신규매출 ".$money."(".$data_m[name].")".number_format($total_money)." <br>";


//2. 팀장 실적 입력
if($id_manager!=$name){  // 팀장과 고객명이 같으면 한번만 누적

$query_m ="select * from member where name='$id_manager' ";  //회원테이블 불러서
$result_m=mysql_query($query_m,$connect);
$data_m = mysql_fetch_array($result_m);

     if($type!="월대차/만기지급"){
     $total_money=$data_m[total_money]+$money; }
     else{ $total_month=$data_m[total_month]+$money; }

          //매출 <회원 정보>업데이트//////////////////////////////////////
          $query="update member set
          total_money='$total_money',
          total_month='$total_month'

          where user_id='$data_m[user_id]' ";
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close;

     echo "팀장 : 신규매출 ".$money."(".$data_m[name].")".number_format($total_money)." <br>";
}


//3. 소개팀장 실적 입력 ************
if($id_incentive!="회사"){
          $query_s ="select * from member where name='$id_incentive' ";  //회원테이블 불러서
          $result_s=mysql_query($query_s,$connect);
          $data_s= mysql_fetch_array($result_s);

               if($type!="월대차/만기지급"){
               $total_money=$data_s[total_money]+$money; }
               else{ $total_month=$data_s[total_month]+$money; }

                    //매출 <회원 정보>업데이트//////////////////////////////////////
                    $query="update member set
                    total_money='$total_money',
                    total_month='$total_month'

                    where user_id='$data_s[user_id]' ";
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;

     echo "소개팀장 : 신규매출 ".$money."(".$data_s[name].")".number_format($total_money)." <br>";
}


//4. 소개팀장의 소개팀장 찾기


while($id_incentive!='회사'){
          $query_c ="select * from member where name='$id_incentive' ";  //소개팀장이었던 애의 회원테이블 불러서
          $result_c=mysql_query($query_c,$connect);
          $data_c = mysql_fetch_array($result_c);

          $id_next=$data_c[id_incentive];  //다음 스폰서를 찾아내서

          $query_m ="select * from member where name='$id_next' ";  //회원테이블 불러서
          $result_m=mysql_query($query_m,$connect);
          $data_m = mysql_fetch_array($result_m);


               if($type!="월대차/만기지급"){
               $total_money=$data_m[total_money]+$money; }
               else{ $total_month=$data_m[total_month]+$money; }

               if(($id_next=='회사') and ($id_top=='회사') ){
                    $total_money=$data_m[total_money];
                    $total_month=$data_m[total_month];}

     echo "-추가 소개팀장 :(".$data_m[name].")".number_format($total_money)." <br>";

                    //매출 <회원 정보>업데이트//////////////////////////////////////
                    $query="update member set
                    total_money='$total_money',
                    total_month='$total_month'

                    where user_id='$data_m[user_id]' ";
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;

                    $id_incentive=$data_m[name];
                    if($id_incentive=='회사'){break;}
}



//4. 본부장 실적 입력

               $query_m ="select * from member where name='$id_top' ";  //회원테이블 불러서
               $result_m=mysql_query($query_m,$connect);
               $data_m = mysql_fetch_array($result_m);

                    if($type!="월대차/만기지급"){
                    $total_money=$data_m[total_money]+$money; }
                    else{ $total_month=$data_m[total_month]+$money; }

                         //매출 <회원 정보>업데이트//////////////////////////////////////
                         $query="update member set
                         total_money='$total_money',
                         total_month='$total_month'

                         where user_id='$data_m[user_id]' ";
                         mysql_query("set names utf8", $connect);
                         mysql_query($query, $connect);
                         mysql_close;
          echo "본부장 : 신규매출 ".$money."(".$data_m[name].")".number_format($total_money)." <br><br>";




}  //계약서 합계 구하기 종료


//회원정보 업데이트 여부 확인
echo "회원정보 업데이트 결과확인 <br><br>";
     $query= "select * from member where id='mem' order by id asc";
     mysql_query("set names ust8", $connect);
     $result= mysql_query($query, $connect);
     while($data = mysql_fetch_array($result)){
     echo "성명 :(".$data[name].")".number_format($data[total_money])." <br>";
      }



?>
