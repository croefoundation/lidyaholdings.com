<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

include('../../lib/lib.php'); //시간,날짜변환외


$id= $_POST[id];
$newtype=$_POST[newtype];
$ctr_no= $_POST[ctr_no];
$name= $_POST[name];

$name="송현주";
//이름이 입력되면 회원정보를 불러와서 아이디를 찾고, 팀장과 그 팀장의 소개자와 본부장을 찾아서 등록한다.
$query_id= "select * from member where name='$name'";
mysql_query("set names ust8", $connect);
$result_id= mysql_query($query_id, $connect);
$dm = mysql_fetch_array($result_id);

$user_id=$dm[user_id]; //유저아이디
$id_manager=$_POST[id_manager];
$id_incentive=$dm[id_incentive];
$id_top=$dm[id_top];


$type= $_POST[type];
$money= $_POST[money];
$money_old= $_POST[money_old];
$money_new= $_POST[money_new];
$ctr_start=$_POST[ctr_start];
$ctr_end=$_POST[ctr_end];
$ctr_date=$_POST[ctr_date];

$rate_cus= $_POST[rate_cus];
//지급율을 직접 입력받는다
$rate_manager= $_POST[rate_manager];
$rate_incentive= $_POST[rate_incentive];
$rate_top= $_POST[rate_top];



// *********************테스트 항목 ********************
$money= 100000000;
$ctr_start="2017-02-10";
$ctr_end="2018-04-30";
$rate_cus=10;
//지급율을 직접 입력받는다
$rate_manager=12;
$rate_incentive=1;
$rate_top=1;


echo "고객 :".$name."<br>";
echo "팀장 : ".$dm[id_manager]."<br>";
echo "오버라이드 :".$dm[id_incentive]."<br>";
echo "본부장 :".$dm[id_top]."<br><br>";


//지급일, 지급액, 지급회차
     //날짜 변환
       $date_tr=$ctr_end; //마감일 기준
       $date_y=substr($date_tr,0,4);
       $date_m=substr($date_tr,5,2);
       $date_d=substr($date_tr,8,2);
       if($date_d=="30"){$date_d=31;}
       if($date_m=="02" and ($date_d=="28"or $date_d=="29")){$date_d=31;}
 //반복구문으로 1계약당 조회후 =팀장지급회수 만큼 디비입력
 //팀장지급율 5%면 5회지급
 //$rate_manager

 //횟수표시 분모에
 if($rate_manager <= 12){
              $period_team = $rate_manager."th"; }else{ $period_team ="12th"; }

//만약12%보다 작으면(즉11%까지는)매월 1%씩 나가고, 12%포함이상은 마감날 한꺼번에 주게..
 if($rate_manager<12){
     for ($i=1; $i <$rate_manager+1 ; $i++) {
           $dt=computeMonth($date_y,$date_m,$date_d,$i-12);   //날짜 말일적용하여 역으로 계산

           $pay_date=$dt;
           $amount=($money*$rate_manager/100)/$rate_manager;
           $period=$i."회/".$period_team;

          //수수료 출력 Test
 echo $pay_date."  ".number_format($amount)."   ".$period." <br>";

            } //for문 종료
          }//조건문 매월1%종료

     else{ //12%이상이면
          for ($i=1; $i <12 ; $i++) {
                $dt=computeMonth($date_y,$date_m,$date_d,$i-12);   //날짜 말일적용하여 역으로 계산

                $pay_date=$dt;
                $amount=($money*$rate_manager/100)/$rate_manager;
                $period=$i."회/".$period_team;

                //수수료 출력 Test
           echo $pay_date."  ".number_format($amount)."   ".$period." <br>";
        }

          $pay_date=$ctr_end;
          $amount=$money*0.01*($rate_manager-11);
          if($rate_manager==12){$period="12회/".$period_team;}else{$period="12회(잔여포함)/".$period_team;}

     //수수료 출력 Test
echo $pay_date."  ".number_format($amount)."   ".$period." <br>";

} //마지막달 12%이상 종료



?>
