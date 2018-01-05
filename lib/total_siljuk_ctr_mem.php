<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외

//******************************************************************************************
//회원실적 계산 루틴 //////////
//2017.08.05
// 각각회원들의 실적을 계산해서 [회원]에 저장함....
//******************************************************************************************
?>
<body style="font-size:10pt">
<?

//먼저<회원등록> 테이블에서 :  각자 전체실적을 일괄 삭제를 한다......
echo "회원정보 삭제 <br>";

$query= "select * from member where id='mem' order by id asc";  //등록순으로 불러와서..
mysql_query("set names ust8", $connect);
$result= mysql_query($query, $connect);
while($data = mysql_fetch_array($result)){ //while문 시작 계약서를 하나둘씩 불러와서..수당테이블에 저장한다.--------------------------------

//전체 삭제하기 --------------------------------
                  $total_money=0; $total_month=0;  $money_live=0; $money_live_m=0; $remoney_lh=0; $remoney_dl=0;
                         //매출 <회원 정보>업데이트//////////////////////////////////////
                         $query="update member set
                         total_money='$total_money',
                         money_live='$money_live',
                         money_live_m='$money_live_m',
                         total_month='$total_month',
                         remoney_lh='$remoney_lh',
                         remoney_dl='$remoney_dl'

                         where user_id='$data[user_id]' ";
                         mysql_query("set names utf8", $connect);
                         mysql_query($query, $connect);
                         mysql_close;
//-----------------------------------------------------------
     }  //삭제완료




//[계약테이블]을 조회해서 우선 불러온다..-----------
//    1.약정기간이 먼저인 순서로 소팅한다--본부장 정하게여..

$bonbu=2000000000;

$query1= "select * from contract order by ctr_start asc";
mysql_query("set names ust8", $connect);
$result1= mysql_query($query1, $connect);

while($data = mysql_fetch_array($result1)){ //while문 시작 계약서를 하나씩 불러와서..수당계산후 수당테이블에 저장한다.--------------------------------
$id= $data[id];
$newtype=$data[newtype];
$ctr_no= $data[ctr_no];
$name= $data[name];

//계약서상 고객,팀장,소개팀장,본부장
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

$state=$data[state]; //상태
$end_type=$data[end_type];
$end_back=$data[end_back];
$end_add=$data[end_add];
$end_remoney=$data[end_remoney];

$new_top="없음";


//1. 고객 매출 입력
//    기존의 매출에 + 신규등록하는 매출을 합하여 전체 매출로 저장한다.
//   일반매출과 월대차로 구분하여 각각 누적한다.
if($id_manager!=$name){  // 팀장과 고객명이 같으면 한번만 누적 ...팀장자신의 매출을 중복하는걸 막자..

$query_m ="select * from member where user_id='$user_id'";  //회원테이블 불러서
$result_m=mysql_query($query_m,$connect);
$data_m = mysql_fetch_array($result_m);

if($type!="월대차/만기지급"){
     $total_money=$data_m[total_money]+$money;
     $total_month=$data_m[total_month];
     if($state=="정상"){
          $money_live=$data_m[money_live]+$money;
          $money_live_m=$data_m[money_live_m];
      }
}
else{ //월대차이면
     $total_money=$data_m[total_money];
     $total_month=$data_m[total_month]+$money;
     if($state=="정상"){
          $money_live=$data_m[money_live];
          $money_live_m=$data_m[money_live_m]+$money;
      }
}

     if($end_type=="재계약(LH)"){$remoney_lh=$data_m[remoney_lh]+$end_remoney;}else{$remoney_lh=$data_m[remoney_lh];}
     if($end_type=="재계약(DL)"){$remoney_dl=$data_m[remoney_dl]+$end_remoney;}else{$remoney_dl=$data_m[remoney_dl];}


     //매출 <회원 정보>업데이트//////////////////////////////////////
     $query="update member set
     total_money='$total_money',
     money_live='$money_live',
     money_live_m='$money_live_m',
     total_month='$total_month',
     remoney_lh='$remoney_lh',
     remoney_dl='$remoney_dl'

     where user_id='$data_m[user_id]' ";
     mysql_query("set names utf8", $connect);
     mysql_query($query, $connect);
     mysql_close;

     echo "1. 고객 : (매출: ".number_format($money).") ".$data_m[name]." : 전체매출(일반)".number_format($total_money)."전체매출(월대차)".number_format($total_month)."   정상매출(일반)".number_format($money_live). "  정상매출(월대차)".number_format($money_live_m)."/ 딜라이트 누적".$remoney_dl."  /리디아 누적".$remoney_lh." <br>";
  } else{
          echo "1.고객 : (매출: ".number_format($money).") ".$id_manager." 본인 팀장매출임 <br>";
         }



//2. 팀장 실적 입력
$query_m ="select * from member where name='$id_manager' ";  //회원테이블 불러서
$result_m=mysql_query($query_m,$connect);
$data_m = mysql_fetch_array($result_m);

    if($new_top=="없음"){
    if($data_m[total_money]>$bonbu){ $new_top=$id_manager;
         echo "<font color='red'>***신 본부장 :(".$new_top.")".number_format($data_m[total_money])." </font><br>";
        $id_top_volume=$data_m[total_money]; }  //신본부장이 있으면 실적을 기록하고
    }

        if($type!="월대차/만기지급"){
             $total_money=$data_m[total_money]+$money;
             $total_month=$data_m[total_month];
             if($state=="정상"){
                  $money_live=$data_m[money_live]+$money;
                  $money_live_m=$data_m[money_live_m];
              }
        }
        else{ //월대차이면
             $total_money=$data_m[total_money];
             $total_month=$data_m[total_month]+$money;
             if($state=="정상"){
                  $money_live=$data_m[money_live];
                  $money_live_m=$data_m[money_live_m]+$money;
              }
        }

             if($end_type=="재계약(LH)"){$remoney_lh=$data_m[remoney_lh]+$end_remoney;}else{$remoney_lh=$data_m[remoney_lh];}
             if($end_type=="재계약(DL)"){$remoney_dl=$data_m[remoney_dl]+$end_remoney;}else{$remoney_dl=$data_m[remoney_dl];}


             //매출 <회원 정보>업데이트//////////////////////////////////////
             $query="update member set
             total_money='$total_money',
             money_live='$money_live',
             money_live_m='$money_live_m',
             total_month='$total_month',
             remoney_lh='$remoney_lh',
             remoney_dl='$remoney_dl'

             where user_id='$data_m[user_id]' ";
             mysql_query("set names utf8", $connect);
             mysql_query($query, $connect);
             mysql_close;

             echo "2. 팀장실적 : (매출: ".number_format($money).") ".$data_m[name]." : 전체매출(일반)".number_format($total_money)."전체매출(월대차)".number_format($total_month)."   정상매출(일반)".number_format($money_live). "  정상매출(월대차)".number_format($money_live_m)."/ 딜라이트 누적".$remoney_dl."  /리디아 누적".$remoney_lh." <br>";




//3. 소개팀장 실적 입력 ************
if($id_incentive!="회사"){   //소개팀장이 회사이면..본부장도 회사이므로-->회사는 본부장에서 한번만 누적함
          $query_s ="select * from member where name='$id_incentive' ";  //회원테이블 불러서
          $result_s=mysql_query($query_s,$connect);
          $data_m= mysql_fetch_array($result_s);

          if($new_top=="없음"){ //
          if($data_m[total_money]>$bonbu){ $new_top=$id_incentive;
                echo "<font color='red'>***신 본부장 :(".$new_top.")".number_format($data_m[total_money])." </font><br>";
                  $id_top_volume=$data_m[total_money]; }  //신본부장이 있으면 실적을 기록하고
          }


                     if($type!="월대차/만기지급"){
                          $total_money=$data_m[total_money]+$money;
                          $total_month=$data_m[total_month];
                          if($state=="정상"){
                               $money_live=$data_m[money_live]+$money;
                               $money_live_m=$data_m[money_live_m];
                           }
                     }
                     else{ //월대차이면
                          $total_money=$data_m[total_money];
                          $total_month=$data_m[total_month]+$money;
                          if($state=="정상"){
                               $money_live=$data_m[money_live];
                               $money_live_m=$data_m[money_live_m]+$money;
                           }
                     }

                          if($end_type=="재계약(LH)"){$remoney_lh=$data_m[remoney_lh]+$end_remoney;}else{$remoney_lh=$data_m[remoney_lh];}
                          if($end_type=="재계약(DL)"){$remoney_dl=$data_m[remoney_dl]+$end_remoney;}else{$remoney_dl=$data_m[remoney_dl];}


                          //매출 <회원 정보>업데이트//////////////////////////////////////
                          $query="update member set
                          total_money='$total_money',
                          money_live='$money_live',
                          money_live_m='$money_live_m',
                          total_month='$total_month',
                          remoney_lh='$remoney_lh',
                          remoney_dl='$remoney_dl'

                          where user_id='$data_m[user_id]' ";
                          mysql_query("set names utf8", $connect);
                          mysql_query($query, $connect);
                          mysql_close;


      echo "3. 소개팀장실적 : (매출: ".number_format($money).") ".$data_m[name]." : 전체매출(일반)".number_format($total_money)."전체매출(월대차)".number_format($total_month)."  정상매출(일반)".number_format($money_live). "  정상매출(월대차)".number_format($money_live_m)."/ 딜라이트 누적".$remoney_dl."  /리디아 누적".$remoney_lh." <br>";
} else{
echo "3.소개팀장 : (매출:".number_format($money).")  ".$id_incentive."회사가 소개자임, 회사는 따로 누적. <br>";
}




//4. 소개팀장의 소개팀장 찾기 //////매출에 따라////.. 맨위 회사까지 반복

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
              echo "<font color='red'>***신 본부장 :(".$new_top.")".number_format($data_m[total_money])." </font><br>";
           $id_top_volume=$data_m[total_money]; }  //신본부장이 있으면 실적을 기록하고
           }

              if($type!="월대차/만기지급"){
                   $total_money=$data_m[total_money]+$money;
                   $total_month=$data_m[total_month];
                   if($state=="정상"){
                        $money_live=$data_m[money_live]+$money;
                        $money_live_m=$data_m[money_live_m];
                    }
              }
              else{ //월대차이면
                   $total_money=$data_m[total_money];
                   $total_month=$data_m[total_month]+$money;
                   if($state=="정상"){
                        $money_live=$data_m[money_live];
                        $money_live_m=$data_m[money_live_m]+$money;
                    }
              }

                   if($end_type=="재계약(LH)"){$remoney_lh=$data_m[remoney_lh]+$end_remoney;}else{$remoney_lh=$data_m[remoney_lh];}
                   if($end_type=="재계약(DL)"){$remoney_dl=$data_m[remoney_dl]+$end_remoney;}else{$remoney_dl=$data_m[remoney_dl];}


      echo "-추가소개팀장 : (매출: ".number_format($money).") ".$data_m[name]." : 전체매출(일반)".number_format($total_money)."전체매출(월대차)".number_format($total_month)."  정상매출(일반)".number_format($money_live). "  정상매출(월대차)".number_format($money_live_m)."/ 딜라이트 누적".$remoney_dl."  /리디아 누적".$remoney_lh." <br>";


          //매출 <회원 정보>업데이트//////////////////////////////////////
          $query="update member set
          total_money='$total_money',
          money_live='$money_live',
          money_live_m='$money_live_m',
          total_month='$total_month',
          remoney_lh='$remoney_lh',
          remoney_dl='$remoney_dl'

          where user_id='$data_m[user_id]' ";
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close;


     $id_incentive=$data_m[name];      //2대팀장을 =소개팀장이라고 하고 다시 반복
     if($id_incentive=="회사"){break;}   //다음 소개팀장이 회사이면  최고 윗단계이므로 종료

}  //for 소개팀장의 소개팀장 찾기 종료



   echo "----------------------------------------------------<br>";




// ////////////////본부장 찾아 ==계약서에 입력/////////////////////
// // 회원실적이 누적이 완료되면... 수당돌리기전 반드시 본부장을 업데이트 할것..
// //반드시 계약번호에 업데이트
//
     if($new_top=="없음"){
      $id_top=$id_top;

      $query_m ="select * from member where name='$id_top'";  //회원테이블 불러서
      $result_m=mysql_query($query_m,$connect);
      $data_m = mysql_fetch_array($result_m);
      $id_top_volume=$data_m[total_money];


} else{$id_top=$new_top;}
                    //계약서 <본부장>업데이트//////////////////////////////////////
                    $query="update contract set
                    id_top='$id_top',
                    id_top_volume='$id_top_volume'

                    where ctr_no='$ctr_no' ";

                    mysql_query("set names utf8", $connect);
                    mysql_query($query, $connect);
                    mysql_close;


}  //계약서 합계 구하기 종료  ******************************************************************


//회원정보 업데이트 여부 확인
echo "입력후 결과확인<br>";
$query= "select * from contract order by ctr_start asc ";
                    mysql_query("set names ust8", $connect);
                    $result= mysql_query($query, $connect);
                    while($data = mysql_fetch_array($result)){
                    echo "|고객명 :".$data[name]."   |팀장:".$data[id_manager]."  |소개팀장:".$data[id_incentive]."  |본부장:".$data[id_top]." 매출:".number_format($data[id_top_volume])."<br>";
               }

?>

</body>
