<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외

//******************************************************************************************
//[계약서 입력후] 1인의 회원실적을 누적입력 루틴 //////////
//   2017.08.15
//  계약서 입력 ----> 실적을 계산해서 [회원]의 실적에  누적함....
//******************************************************************************************
?>


<?
///////////////////////////////////////////////////////////////////
// 계약서가 입력되어 항목을 읽었다는 전제하에 실행한다.
/////////////////////////////////////////////////////////////////

$bonbu=2000000000;

//1. 고객 매출 입력
//    기존의 매출에 + 신규등록하는 매출을 합하여 전체 매출로 저장한다.
//   일반매출과 월대차로 구분하여 각각 누적한다.
if($id_manager!=$name){  // 팀장과 고객명이 같으면 한번만 누적 ...팀장자신의 매출을 중복하는걸 막자..

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

     echo "1. 고객 : (매출: ".number_format($money).") ".$data_m[name]." : ".number_format($total_money)." <br>";
  } else{
          echo "1.고객 : (매출: ".number_format($money).") ".$id_manager." 본인 팀장매출임 <br>";
         }


//2. 팀장 실적 입력
$query_m ="select * from member where name='$id_manager' ";  //회원테이블 불러서
$result_m=mysql_query($query_m,$connect);
$data_m = mysql_fetch_array($result_m);

    if($new_top=="없음"){
    if($data_m[total_money]>$bonbu){ $new_top=$id_manager;
        echo "***신 본부장 :(".$new_top.")".number_format($data_m[total_money])." <br>"; } }

     if($type!="월대차/만기지급"){
     $total_money=$data_m[total_money]+$money; }
     else{ $total_month=$data_m[total_month]+$money; }

          //매출 <회원 정보>업데이트//////////////////////////////////////
          $query="update member set
          total_money='$total_money',
          total_month='$total_month'

          where user_id='$data_m[user_id]' ";  //팀장
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close;

echo "2. 팀장실적 : (매출: ".number_format($money).") ".$data_m[name]." : ".number_format($total_money)." <br>";

//3. 소개팀장 실적 입력 ************
if($id_incentive!="회사"){   //소개팀장이 회사이면..본부장도 회사이므로-->회사는 본부장에서 한번만 누적함
          $query_s ="select * from member where name='$id_incentive' ";  //회원테이블 불러서
          $result_s=mysql_query($query_s,$connect);
          $data_s= mysql_fetch_array($result_s);

          if($new_top=="없음"){ //
          if($data_s[total_money]>$bonbu){ $new_top=$id_incentive;
              echo "***신 본부장 :(".$new_top.")".number_format($data_s[total_money])." <br>"; } }

               if($type!="월대차/만기지급"){
               $total_money=$data_s[total_money]+$money; }
               else{ $total_month=$data_s[total_month]+$money; }

                    //매출 <회원 정보>업데이트//////////////////////////////////////
                    $query="update member set
                    total_money='$total_money',
                    total_month='$total_month'

                    where user_id='$data_s[user_id]' "; //소개팀장
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;

     echo "3. 소개팀장 : (매출:".number_format($money).")  ".$data_s[name].":".number_format($total_money)." <br>";
} else{
echo "3.소개팀장 : (매출:".number_format($money).")  ".$id_incentive."회사가 소개자임, 회사는 따로 누적. <br>";
}


//4. 소개팀장의 소개팀장 찾기 //////매출에 따라////
if($id_incentive!='회사'){

for ($i=0; $i <10000 ; $i++) {

          $query_c ="select * from member where name='$id_incentive' ";  //소개팀장이었던 애의 회원테이블 불러서
          $result_c=mysql_query($query_c,$connect);
          $data_c = mysql_fetch_array($result_c);

          $id_next=$data_c[id_incentive];  //다음 스폰서를 찾아내서--  고객입장에서 2대 팀장

          $query_m ="select * from member where name='$id_next' ";  //회원테이블 불러서
          $result_m=mysql_query($query_m,$connect);
          $data_m = mysql_fetch_array($result_m);

          if($new_top=="없음"){ //
          if($data_m[total_money]>$bonbu){ $new_top=$id_next;
              echo "<font color='red'>***신 본부장 :(".$new_top.")".number_format($data_m[total_money])." </font><br>"; } }

               if($type!="월대차/만기지급"){
               $total_money=$data_m[total_money]+$money; }
               else{ $total_month=$data_m[total_month]+$money; }

               if(($id_next=='회사') ){   //2대팀장이 회사이면 누적하지 않기..회사는 따로 실적을 입력할테니..
                    $total_money=$data_m[total_money];
                    $total_month=$data_m[total_month];
                    echo "-추가 소개팀장 : ".$data_m[name]."[별도누적]/기존(".number_format($total_money).") <br>";  //2대팀장 출력
               }else{
     echo "-추가 소개팀장 : ".$data_m[name]." :".number_format($total_money)." <br>";  //2대팀장 출력
          }
                    //매출 <회원 정보>업데이트//////////////////////////////////////
                    $query="update member set
                    total_money='$total_money',
                    total_month='$total_month'

                    where user_id='$data_m[user_id]' ";
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;

                    $id_incentive=$data_m[name];      //2대팀장을 =소개팀장이라고 하고 다시 반복
                    if($id_incentive=="회사"){break;}   //소개팀장이 회사이면  당연히 화일문 위에서 통과 못하니 종료..최고 윗단계이므로
               }  //for 소개팀장의 소개팀장 찾기 종료
} //회사가 아닐때만 실행





//4. 회사 실적 입력
               $query_m ="select * from member where name='회사' ";  //회원테이블 불러서
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

          echo "4.회사실적 : (매출:".number_format($money).")   ".$data_m[name].":".number_format($total_money)." <br>";




////////////////////본부장 찾기./////////////////////
// 회원실적이 누적이 완료되면... 회원테이블 뒤져서
//반드시 계약번호에 업데이트

if($new_top=="없음"){
      $id_top=$id_top; } else{$id_top=$new_top;}
                    //계약서 <본부장>업데이트//////////////////////////////////////
                    $query="update contract set
                    id_top='$id_top'
                    where ctr_no='$ctr_no' ";

                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;


?>
