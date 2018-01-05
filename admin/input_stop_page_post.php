<? header("content-type:text/html; charset=UTF-8"); ob_start;?>

<?
include('../lib/db_connect.php');
$connect = dbconn(); //DB컨넥트
$member = member();  //회원정보

if ($member[user_id] != "admin") Error("관리자 메뉴입니다.");
include('../lib/lib.php'); //시간,날짜변환외

include('../_header.php'); ?>

<?
$stop_state = $_POST['stop_state'];
$ctr_no=$_POST['ctr_no'];
$stop_date=$_POST['stop_date'];
$name=$_POST['name'];
$id_manager=$_POST['id_manager'];
$id_incentive =$_POST['id_incentive'];
$id_top =$_POST['id_top'];

$minus_c =$_POST['minus_c'];
$minus_t =$_POST['minus_t'];
$minus_i =$_POST['minus_i'];
$minus_top =$_POST['minus_top'];
$memo_x =$_POST['memo_x'];

$end_back=$_POST['money_back'];

$now=date("Ymd H:i:s");

//1.중도해지 처리 루틴*************************************************************************

if ($stop_state=="stop_cus") {

//1. 고객 계약서 업데이트
$query="update contract set
          state = '종료',
          end_type = '중도해지',
          stop_date ='$stop_date',
          stop_exe_date='$now',
          sum_cus ='0',
          sum_manager ='0',
          sum_incentive ='0',
          sum_top='0',

          gongje_cus ='$minus_c',
          gongje_manager ='$minus_t',
          gongje_incentive ='$minus_i',
          end_back='$end_back',

          memo_x='$memo_x'

          where ctr_no='$ctr_no'";
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close;



//2.수당테이블에서 해당계약으로 인한 발생분을 삭제

$query="select * from payment where ctr_no='$ctr_no' order by pay_date asc"; //중도해지 된  계약번호에 대해서  수당을 불러와서
mysql_query("set names utf8", $connect);
$result=mysql_query($query, $connect);
while ($data=mysql_fetch_array($result)) {
          $date1=strtotime($data[pay_date]);
          $date2=strtotime($stop_date);  //중도해지를 요청한 날 기준으로
    if($date1<=$date2){  // 그 이전에 받은 수당은 그대로두고, 이후는 삭제, 사유에 중도해지를 표시한다.

         $query="update payment set
                   pay_state = '중도해지',
                   comment='중도해지',
                   stop_exe_date='$now',
                   stop_date='$stop_date',
                   banpum_ctr='$ctr_no'

         where no='$data[no]'";
         mysql_query("set names utf8", $connect);
         mysql_query($query, $connect);
         mysql_close;

   }else{
      $query="delete from payment where no='$data[no]' "; //행을 삭제
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close;
       }
} //계약번호에 의해 발생한 수당을 두부류로 나누어서 정리..


//마지막 상환할 금액을 수당으로 찍어놓는다.....

     $query_r= "select * from contract where ctr_no='$ctr_no'";
     mysql_query("set names ust8", $connect);
     $result_r= mysql_query($query_r, $connect);
     $data = mysql_fetch_array($result_r);  //유저 아이디 찾기위해서....

     $user_id=$data[user_id]; //유저아이디
     $pay_name= '고객 중도해지금액';
     $pay_type= '중도해지금';
     $pay_date= $stop_date;
     $amount= $end_back;
     $period="일시지급";
     $ctr_name=$data[name];
     $comment="원금:".$data[money]."-이자지급분:".$minus_c;
     $pay_state="중도해지";


     //DB입력
     $query="INSERT INTO payment (name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state, stop_date)
            VALUES ('$name', '$user_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state','$stop_date')";
     mysql_query("set names utf8", $connect);
     mysql_query($query, $connect);
     mysql_close; //끝내기.




//3.[팀장커미션분]기지급된 수당을 앞으로 받을 수당에서 삭제
$gongje=$minus_t; //공제할 금액을 체크
if($gongje>0){ //공제할 금액이 있으면

          $query="select * from payment where name='$id_manager' order by pay_date asc"; //팀장이름으로 된 수당을 불러와서
          mysql_query("set names utf8", $connect);
          $result=mysql_query($query, $connect);
          $i=1;
          while ($data=mysql_fetch_array($result)) {

               $date1=strtotime($data[pay_date]);
               $date2=strtotime($stop_date);  //중도해지를 요청한 날 기준으로
          if($date1>$date2){  // 그 이후에 받을 수당에서  공제해 나간다.

               if($gongje>$data[amount]){  //공제금액이 줄 금액보다 크면
                    $amount=-round($data[amount]);   //수당을 공제하고
                    $gongje=$gongje-$data[amount];   //공제잔액을 줄이고

                    $banpum_ctr=$ctr_no."(".$name.")";   //반품 계약서번호를 기록하고
                    $banpum_process=$i."차공제(".number_format($amount)."/".number_format($data[amount]).")";          //반품공제 횟수를 기록하고
                    $gongje_process="잔액(".number_format($gongje).")/".number_format($minus_t);  //공제잔액/전체공제액

                    $query="update payment set
                              pay_state = '공제',
                              stop_exe_date='$now',
                              stop_date='$stop_date',
                              amount='0',
                              banpum_ctr='$banpum_ctr',
                              gongje_process='$gongje_process',
                              banpum_process='$banpum_process'

                    where no='$data[no]'";
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;
                    $i++;

               }else{   // 공제를 완료하는 루틴
                    $amount=round($data[amount])-round($gongje);

                    $banpum_ctr=$ctr_no."(".$name.")";
                    $banpum_process=$i."차공제완료(".number_format(-$gongje)."/".number_format($data[amount]).")";          //반품공제 횟수를 기록하고
                    $gongje_process="잔액(0)/".number_format($minus_t);  //공제잔액/전체공제액

                    $query="update payment set
                              pay_state = '공제',
                              stop_exe_date='$now',
                              stop_date='$stop_date',
                              amount='$amount',
                              banpum_ctr='$banpum_ctr',
                              gongje_process='$gongje_process',
                              banpum_process='$banpum_process'

                    where no='$data[no]'";
                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;

                    $gongje=0;
                    if($gongje=="0")break; //다 공제했으면 종료
                    }

          }//공제일 이전 수당이면 기 지급된거라 할수 없이 통과하시고...
               }//while문 수당공제완료
} //여기까지 공제할 게 있으면..공제루틴을 돌려라.. 없으면 안돌리지뭐...


///////4.[소개팀장 인센티브분]기지급된 수당을 앞으로 받을 수당에서 삭제

     $gongje=$minus_i; //공제할 금액을 체크
if($gongje>0){ //공제할 금액이 있으면


     $query="select * from payment where name='$id_incentive' order by pay_date asc"; //소개팀장이름으로 된 수당을 불러와서
     mysql_query("set names utf8", $connect);
     $result=mysql_query($query, $connect);
     $i=1;
     while ($data=mysql_fetch_array($result)) {

          $date1=strtotime($data[pay_date]);
          $date2=strtotime($stop_date);  //중도해지를 요청한 날 기준으로
     if($date1>$date2){  // 그 이후에 받을 수당에서  공제해 나간다.


          if($gongje>$data[amount]){  //공제금액이 줄 금액보다 크면
               $amount=-round($data[amount]);   //수당을 공제하고
               $gongje=$gongje-$data[amount];   //공제잔액을 줄이고

               $banpum_ctr=$ctr_no."(".$name.")";   //반품 계약서번호를 기록하고
               $banpum_process=$i."차공제(".number_format($amount)."/".number_format($data[amount]).")";          //반품공제 횟수를 기록하고
               $gongje_process="잔액(".number_format($gongje).")/".number_format($minus_i);  //공제잔액/전체공제액

               $query="update payment set
                         pay_state = '공제',
                         stop_exe_date='$now',
                         stop_date='$stop_date',
                         amount='0',
                         banpum_ctr='$banpum_ctr',
                         gongje_process='$gongje_process',
                         banpum_process='$banpum_process'

               where no='$data[no]'";
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close;
               $i++;

          }else{   // 공제를 완료하는 루틴
               $amount=round($data[amount])-round($gongje);

               $banpum_ctr=$ctr_no."(".$name.")";
               $banpum_process=$i."차공제완료(".number_format(-$gongje)."/".number_format($data[amount]).")";          //반품공제 횟수를 기록하고
               $gongje_process="잔액(0)/".number_format($minus_i);  //공제잔액/전체공제액

               $query="update payment set
                         pay_state = '공제',
                         stop_exe_date='$now',
                         stop_date='$stop_date',
                         amount='$amount',
                         banpum_ctr='$banpum_ctr',
                         gongje_process='$gongje_process',
                         banpum_process='$banpum_process'

               where no='$data[no]'";
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close;

               $gongje=0;
               if($gongje=="0")break; //다 공제했으면 종료
               }

          }//공제일 이전 수당이면 기 지급된거라 할수 없이 통과하시고...

          }//while문 수당공제완료

} //여기까지 공제할 게 있으면..공제루틴을 돌려라.. 없으면 안돌리지뭐...

          //5.[본부장 성과급]기지급된 수당을 앞으로 받을 수당에서 삭제
            //중도해지는 본부장이 수당을 받지 않은 상태에서 해지 하므로 삭제할 게  없음..


} //if문 --****중도해지 처리 완료*******






//**************************중도상환처리 루틴 **************************************************
if ($stop_state=="stop_com") {



     //1. 고객 계약서 업데이트
     $query="update contract set
               state = '종료',
               end_type = '중도상환',
               stop_date ='$stop_date',
               stop_exe_date='$now',
               sum_cus ='$minus_c',
               sum_manager ='$minus_t',
               sum_incentive ='$minus_i',
               sum_top='0',

               end_back='$end_back',

               memo_x='$memo_x'

               where ctr_no='$ctr_no'";
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close;



     //2.수당테이블에서 해당계약으로 인한 발생분을 삭제

     $query="select * from payment where ctr_no='$ctr_no' order by pay_date asc"; //중도상환 된  계약번호에 대해서  수당을 불러와서
     mysql_query("set names utf8", $connect);
     $result=mysql_query($query, $connect);
     while ($data=mysql_fetch_array($result)) {
               $date1=strtotime($data[pay_date]);
               $date2=strtotime($stop_date);  //중도상환를 요청한 날 기준으로
         if($date1<=$date2){  // 그 이전에 받은 수당은 그대로두고, 이후는 삭제, 사유에 중도상환을 표시한다.

              $query="update payment set
                        pay_state = '중도상환',
                        comment='중도상환',
                        stop_exe_date='$now',
                        stop_date='$stop_date',
                        banpum_ctr='$ctr_no'

              where no='$data[no]'";
              mysql_query("set names utf8", $connect);
              mysql_query($query, $connect);
              mysql_close;

        }else{
           $query="delete from payment where no='$data[no]'"; //행을 삭제
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close;
            }
     } //계약번호에 의해 발생한 수당을 두부류로 나누어서 정리..


//마지막 상환할 금액을 수당으로 찍어놓는다.....

     $query_r= "select * from contract where ctr_no='$ctr_no'";
     mysql_query("set names ust8", $connect);
     $result_r= mysql_query($query_r, $connect);
     $data = mysql_fetch_array($result_r);  //유저아이디 찾을라고...

     $user_id=$data[user_id]; //유저아이디
     $pay_name= '고객 중도상환금액';
     $pay_type= '중도상환금';
     $pay_date= $stop_date;
     $amount= $end_back;
     $period="일시지급";
     $ctr_name=$data[name];
     $comment="원금:".$data[money]."+이자:".$minus_c;
     $pay_state="중도상환";


     //DB입력
     $query="INSERT INTO payment (name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state, stop_date)
            VALUES ('$name', '$user_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state','$stop_date')";
     mysql_query("set names utf8", $connect);
     mysql_query($query, $connect);
     mysql_close; //끝내기.





} //if문 --****중도상환 처리 완료*******

if ($stop_state == 'stop_cus') {
    $state = "중도해지(고객사유)";
} else {
    $state = "중도상환(회사사유)";
}
?>

<script>
window.alert('<?=$state?>가 처리되었습니다.');
location.href='../member/list_pay.php';
</script>
