
<?
///1년/만기형-고객1////////////////////////////////////////////////////////////////////////////
//팀장:매월1%씩받다가 마감때 나머지 다받아/인센티브1/본부장1 ********************************

//1.고객 수수료지급 루틴 *******************************************************
if($rate_cus){
          $pid = "pay"; //수당게시판
          $pname = $name;
          $puser_id = $user_id;

          $pay_type="pay_c";
          $pay_name="고객(약정이자)";

          //지급일, 지급액, 지급회차
          $pay_date=$ctr_end; //만기일1회지급
          $amount=($money*$rate_cus)/100;
          $period="만기지급";

          //수수료 발생근거
          $ctr_name=$name;
          $comment=date("Ymd H:i:s");
          $pay_state="예정";

          //DB입력
          $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                  VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close; //끝내기.

     }//1.고객수수료 지급 종료----



//2.팀장 커미션지급 루틴 ****************************************************************
if ($rate_manager) {

          $pid="pay"; //팀장커미션
          $pname=$id_manager;

               //팀장 아이디 및 회원정보 조회
               $qt= "select * from member where name='$id_manager' ";
               mysql_query("set names ust8",$connect);
               $rt= mysql_query($qt, $connect);
               $mt= mysql_fetch_array($rt);
          $puser_id=$mt[user_id];
          $pay_type="pay_t";
          $pay_name="팀장(커미션)";


          //수수료 발생근거
          $ctr_name=$name;
          $comment=date("Ymd H:i:s");
          $pay_state="예정";

          //지급일, 지급액, 지급회차
               //날짜 변환
                 $date_tr=$ctr_end; //마감일 기준
                 $date_y=substr($date_tr,0,4);
                 $date_m=substr($date_tr,5,2);
                 $date_d=substr($date_tr,8,2);

                 if($date_d=="30"){$date_d=31;} //역계산을 할때 오류수정...마감일 기준으로하다보니..
                 if($date_m=="02" and ($date_d=="28"or $date_d=="29")){$date_d=31;}
      //반복구문으로 1계약당 조회후 =팀장지급회수 만큼 디비입력
      //매월 1%씩 지급- 팀장지급율 5%면 5회지급
                //$rate_manager


     // 12%이상 지급하면 마감때 일시지급 루틴..*************************
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

                     //여기까지 우선 수수료 DB입력
                     //DB입력 12%미만
                     $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                             VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
                     mysql_query("set names utf8", $connect);
                     mysql_query($query, $connect);
                     mysql_close; //끝내기.

                       } //for문 종료
                     }//조건문 매월1%종료

                else{ //12%이상이면
                     for ($i=1; $i <12 ; $i++) {
                           $dt=computeMonth($date_y,$date_m,$date_d,$i-12);   //날짜 말일적용하여 역으로 계산

                           $pay_date=$dt;
                           $amount=($money*$rate_manager/100)/$rate_manager;
                           $period=$i."회/".$period_team;

                           //DB입력  12%이상
                           $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                                   VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
                           mysql_query("set names utf8", $connect);
                           mysql_query($query, $connect);
                           mysql_close; //끝내기.

                   }

                     $pay_date=$ctr_end;
                     $amount=$money*0.01*($rate_manager-11);
                     if($rate_manager==12){$period="12회/".$period_team;}else{$period="12회(잔여포함)/".$period_team;}

                     //DB입력
                     $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                            VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
                     mysql_query("set names utf8", $connect);
                     mysql_query($query, $connect);
                     mysql_close; //끝내기.

           } //마지막달 12%이상 종료



} //2.팀장 커미션 매월 1%씩 지급 종료--



//3.팀장 인센티브지급 루틴 ****************************************************************
          if ($rate_incentive) {

               $pid="pay"; //수당게시판
               $pname=$id_incentive;

                    //팀장 아이디 및 회원정보 조회
                    $qt= "select * from member where name='$id_incentive' ";
                    mysql_query("set names ust8",$connect);
                    $rt= mysql_query($qt, $connect);
                    $mt= mysql_fetch_array($rt);
               $puser_id=$mt[user_id];

               $pay_type="pay_i";
               $pay_name="팀장(인센티브)";

               //수수료 발생근거
               $ctr_name=$name;
               $comment=date("Ymd H:i:s");
               $pay_state="예정";

               //지급일, 지급액, 지급회차
                    //날짜 변환
                      $date_tr=$ctr_end; //마감일 기준
                      $date_y=substr($date_tr,0,4);
                      $date_m=substr($date_tr,5,2);
                      $date_d=substr($date_tr,8,2);

                        if($date_d=="30"){$date_d=31;} //역계산을 할때 오류수정...마감일 기준으로하다보니..
                        if($date_m=="02" and ($date_d=="28"or $date_d=="29")){$date_d=31;}

               $dt=computeMonth($date_y,$date_m,$date_d,-11);   //날짜 말일적용하여 계산

               $pay_date=$dt;
               $amount=($money*$rate_incentive/100);
               $period="익월1회";

               //DB입력
               $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                       VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close; //끝내기.

          } //3.팀장 인센티브 1회 지급 종료--



//4.본부장 성과급 지급 루틴 ****************************************************************
          if ($rate_top) {

               $pid="pay"; //수당게시판
               $pname=$id_top;

                    //팀장 아이디 및 회원정보 조회
                    $qt= "select * from member where name='$id_top' ";
                    mysql_query("set names ust8",$connect);
                    $rt= mysql_query($qt, $connect);
                    $mt= mysql_fetch_array($rt);
               $puser_id=$mt[user_id];

               $pay_type="pay_top";
               $pay_name="본부장(성과급)";

               //수수료 발생근거
               $ctr_name=$name;
               $comment=date("Ymd H:i:s");
               $pay_state="예정";

               //지급일, 지급액, 지급회차

               $pay_date=$ctr_end; //마감일 1회지급
               $amount=($money*$rate_top/100);
               $period="만기지급";

               //DB입력
               $query="INSERT INTO payment (id, name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state)
                       VALUES ('$pid', '$pname', '$puser_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state')";
               mysql_query("set names utf8", $connect);
               mysql_query($query, $connect);
               mysql_close; //끝내기.

          } //4.본부장 성과급 1회 지급 종료--

?>
