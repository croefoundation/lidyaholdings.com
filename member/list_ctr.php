<? header("content-type:text/html; charset=UTF-8"); ob_start;?>
<html>

<?
include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if(!$member[user_id])Error_member();
include('../lib/lib.php'); //시간함수외
?>


<!-- Top menu -->
<? include('../_header.php');
   include('../admin/dashboard.php');//dashboard
?>


<body>

    <div class="p-y-0 " style="background-color:#; margin:40px;">
        <div class="wraper">

            <!-- page title row -->
            <div class="page-title">

                    <h3 class="title text-danger"><b>계약조건별 검색 </b></h3>

            </div> <!-- End row -->


            <!-- dashboard -->
            <div class="row">
                    <div class="col-md-12">
                    <table class="table-bordered" style="font-size:12px; line-height:25px; width:100%;">
                    <tr>
                         <td class="td-left bg-danger" style="padding-left:20px; width:200px;">전체 계약</td><td class="text-center td-left2 text-danger"><?=number_format($total_money)?>원 / <?=$tnt?>건</td>
                         <td class="td-left bg-danger" style="padding-left:20px; width:200px;">전체지급(이자+수수료)</td><td class="text-center td-left2 text-danger"><?=number_format($total_int + $total_bonus)?>/</td>
                    </tr>
                    <tr>
                         <td class="td-left1 bg-primary" style="padding-left:20px;">(1)총계약금액(월대차제외)</td><td class="text-center text-primary td-left"><?=number_format($member[total_money])?>원 / <?=($tnt-$tnm)?>건</td>
                         <td class="td-left1 bg-primary" style="padding-left:20px;">(1)고객이자 총액</td><td class="text-center text-primary  td-left"><?=number_format($total_int)?>원</td>

                    </tr>
                    <tr>

                         <td class="td-left bg-primary" style="padding-left:20px;">(2)월대차(약정금액)</td><td class="text-center td-left text-primary"><?=number_format($total_month)?>원 / <?=$tnm?>건</td>
                         <td class="td-left bg-primary" style="padding-left:20px;">(2)수수료발생 총액</td><td class="text-center td-left text-primary"><?=number_format($total_bonus)?>원</td>
                    </tr>


                    <tr>
                         <td class="td-left1 bg-gray" style="padding-left:20px; color:white;">정상유지/종료건수</td><td class="text-center text-primary  td-left1"><?=$cnt_live?>건/<?=($tnt-$cnt_live)?>건</td>

                         <td class="td-left1 bg-pink" style="padding-left:20px; color:white;">1.재계약(리디아)건수</td><td class="text-center text-danger  td-left1"><?=number_format($tn_old)?>건</td>


                    </tr>

                    <tr>
                         <td class="td-left bg-gray" style="padding-left:20px;"> - 정상유지 합계(월대차제외)</td><td class="text-center td-left1 text-danger"><?=number_format($total_money_t)?>원</td>

                         <td class="td-left bg-pink" style="padding-left:20px; color:white;"> - 재계약(리디아)이월총액)</td><td class="text-center td-left1 text-danger"><?=number_format($total_old)?>원</td>

                    </tr>



                    <tr>

                         <td class="td-left bg-gray" style="padding-left:20px;"> - 정상유지 합계(월대차만)</td><td class="text-center td-left1 text-primary"><?=number_format($total_month)?>원</td>
                         <td class="td-left2 bg-rw" style="padding-left:20px;">2.재계약(딜라이트)건수</td><td class="text-center text-danger td-left2"><?=number_format($DL)?>건</td>
                    </tr>
                    <tr>
                         <td class="td-left2 bg-danger" style="padding-left:20px;">(1)실매출등록</td><td class="text-center text-danger  td-left2"><?=number_format($money_real)?>원 /  <?=$cnt_real?>건</td>
                         <td class="td-left2  bg-rw" style="padding-left:20px;">- 재계약(딜라이트)이월금액</td><td class="text-center text-danger td-left2"><?=number_format($money_DL)?>원</td>
                    </tr>
                    <tr>
                         <td class="td-left2  bg-danger" style="padding-left:20px;">(2)재매출(LH)등록</td><td class="text-center text-danger td-left2"><?=number_format($total_old)?>원 / <?=$tn_old?>건</td>
                         <td class="td-left2  bg-danger" style="padding-left:20px;">3.만기상환 (합계/건수)</td><td class="text-center text-danger td-left2"><?=number_format($money_full)?>원 / <?=$cnt_full?>건</td>
                    </tr>
                    </table>
               </div>
               </div>

<br>


            <!-- Detail list-->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">




                        <!-- 검색 항목-->
                        <?
                        $Search_text=$_GET[Search_text];
                        $Search_mode=$_GET[Search_mode];

                        $_page=$_GET[_page];
                        $href=$_GET[href];

                        if($member[level]=="L" or "B" ) {
                            $view_total = 500; //한 페이지에 30(설정할것)개 게시글이 보인다.
                       }else{$view_total =20;}

                        $href = "&Search_mode=$Search_mode&Search_text=$Search_text";

                        if(!$_page)($_page=1); //페이지 번호가 지정이 안되었을 경우
                        $page= ($_page-1)*$view_total;
                        ?>

                        <!-----------게시판 출력-------------------->

                        <div class="panel-heading">
                            <h2 class="panel-title text-">계약 내역(Detail List)</h2>
                        </div>

                        <!--list-table-->
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">

                                 <div class="text-right">   <!---게시물 검색--->
                                     <form action='<?=$PHP_SELE?>'>
                                         <td height='20' colspan='5' bgcolor='#FFFFFF' align='right'>
                                             <label class="btn btn-default " >검색조건 &nbsp;
                                             <select name='Search_mode' class="input-sm">
                                                 <option value='0'>전체검색에서
                                                 <option value='1'>계약자 조회
                                                 <option value='2'>팀장별 조회
                                                 <option value='3'>계약종류
                                                 <option value='4'>시작일로 조회
                                                 <option value='5'>종료일로 조회
                                                 <option value='6'>입금(신규/재계약LH)조회
                                                 <option value='7'>종료구분 조회

                                             </select>

                                             <input type='text' name='Search_text' size='15' class="input-sm">
                                             <input class="btn btn-inverse btn-sm" type='submit' value='Search'>
                                             &nbsp;
                                             <input class="btn btn-default btn-sm" type='reset' value='Reset' onclick='location.reload();'>
                                             &nbsp;
                                         </td>
                                     </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-bordered" style="font-size:11px; width:100%; color:black; line-height:25px">
                                        <thead class="text-white" style="background-color:#000; line-height:30px;">

                                            <tr>
                                                <th class="text-center">NO</th>
                                                <th class="text-center">계약상태</th>
                                                <th class="text-center">입금구분</th>
                                                <th class="text-center">종료구분</th>

                                                <th class="text-center">계약번호</th>
                                                <th class="text-center">계약자</th>
                                                <th class="text-center">계약종류</th>
                                                <th class="text-center">약정금액</th>
                                                <th class="text-center">약정기간</th>

                                                <th class="text-center">고객이율</th>
                                                <th class="text-center">팀장이율</th>
                                                <th class="text-center">인센티브</th>
                                                <th class="text-center">본부장율</th>

                                                <th class="text-center">담당팀장</th>
                                                <th class="text-center">소개팀장</th>
                                                <th class="text-center">본부장</th>
                                                <th class="text-center">본부매출누적</th>

                                                <th class="text-center">재계약(+신/구)</th>
                                                <th class="text-center">구계약</th>

                                                <th class="text-center bg-danger">X-상환금액</th>
                                                <th class="text-center bg-warning">X+추가</th>
                                                <th class="text-center bg-success">X재계약예정</th>

                                                <th class="text-center">지급현황</th>
                                                <th class="text-center">정보수정</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                             <?
                                       if($member[level]=='C'){
                                       $query_count = "select count(*) from contract where user_id='$member[user_id]'";
                                       $query = "select * from contract where user_id='$member[user_id]' order by no desc limit $page, $view_total";
                                       $query_t = "select * from contract where user_id='$member[user_id]'";
                                       }

                                       if($member[level]=='B'){
                                       $query_count ="select count(*) from contract where id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]' ";
                                       $query ="select * from contract where id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]' order by no desc limit $page, $view_total";
                                       $query_t ="select * from contract where id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]'";
                                       }

                                       if($member[level]=='L'){
                                       $query_count ="select count(*) from contract where id='ctr'";
                                       $query ="select * from contract where id='ctr' order by no desc limit $page, $view_total";
                                       $query_t ="select * from contract where id='ctr'";
                                       }
                                       ?>

                                            <!----계약 테이블에서 조회----------->
                                            <?
                                            //$where="name";

                              //검색할 종목을 선택 했을 때.*********************************
                                  if($Search_text){
                                                      if($Search_mode==1) $tmp="name";
                                                      if($Search_mode==2) $tmp="id_manager";
                                                      if($Search_mode==3) $tmp="type";
                                                      if($Search_mode==4) $tmp="ctr_start";
                                                      if($Search_mode==5) $tmp="ctr_end";
                                                      if($Search_mode==6) $tmp="newtype";
                                                      if($Search_mode==7) $tmp="end_type";

                                                      //전체에서 검색
                                                      if($Search_mode==0){
                                         $where="(name like '%$Search_text%' or type like '%$Search_text%' or ctr_end like '%$Search_text%' or end_type like '%$Search_text%'
                                                              or ctr_no like '%$Search_text%' or money like '%$Search_text%' or
                                                              id_top like '%$Search_text%' or id_incentive like '%$Search_text%' or
                                                               id_manager like '%$Search_text%' or newtype like '%$Search_text%' or state like '%$Search_text%'
                                                              or rate_cus like '%$Search_text%' or rate_manager like '%$Search_text%' or ctr_date like '%$Search_text%' )"; //검색조건
                                                             }else{
                                                            $where="$tmp like '%$Search_text%'";
                                                      }  //검색어 선택

                                         if($member[level]=='C'){
                                           $query_count = "select count(*) from contract where $where and user_id='$member[user_id]'";
                                           $query = "select * from contract where $where and user_id='$member[user_id]' order by no desc limit $page, $view_total";
                                           $query_t = "select * from contract where $where and user_id='$member[user_id]'";
                                           }

                                         if($member[level]=='B'){
                                            $query_count = "select count(*) from contract where $where and (id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]') ";
                                            $query ="select * from contract where $where and (id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]')  order by no desc limit $page, $view_total";
                                            $query_t = "select * from contract where $where and (id_manager='$member[name]' or id_incentive='$member[name]' or user_id='$member[user_id]')";
                                            }

                                            if($member[level]=='L'){
                                            $query_count ="select count(*) from contract where $where and id='ctr'"; //조건에 맞는 갯수세게
                                            $query ="select * from contract where $where and id='ctr' order by no desc limit $page, $view_total";
                                            $query_t ="select * from contract where $where and id='ctr'";//조건에 맞는 쿼리를 저장
                                            }



                                       }  //검색어 있을경우 여기까지 쿼리 조건을 찾아서 출력준비 ************************





/////////////////////////////////////////////////////////////////////////////////////////////////////////
                                        //게시물 총갯수 파악//페이지 결정을 위해
                                            mysql_query("set names utf8");  //언어셋 utf8
                                            $result1= mysql_query($query_count, $connect);
                                            $temp= mysql_fetch_array($result1);
                                            $totals= $temp[0];

                                        // 조건에 맞는 게시물 쿼리
                                            $cnt=(($_page-1)*$view_total)+1; //매 페이지수 시작할때 NO번호시작
                                            $result=mysql_query($query,$connect);
                                            while($data = mysql_fetch_array($result)){ ?>


                                            <!-- 종료방식에 따른 배경색 표시 :재계약(LH),재계약(DL),만기상환,중도해지,중도상환 -->
                                            <?if($data[newtype]=="재계약(LH)"){?>
                                            <tr style="color:blue; background-color:#dfeee0"><?}?>

                                                 <?if($data[end_type]=="재계약(LH)"){?>
                                                 <tr style="background-color:#32d710"><?}?>

                                                  <?if($data[end_type]=="재계약(DL)"){?>
                                                      <tr style="color:white; background-color:#198e8e"><?}?>

                                                  <?if($data[end_type]=="만기상환"){?>
                                                       <tr style="background-color:#f1941b"><?}?>

                                                  <?if($data[end_type]=="중도해지"){?>
                                                        <tr style="color:white; background-color:#8e230c"><?}?>
                                                   <?if($data[end_type]=="중도상환"){?>
                                                       <tr style="background-color:#fa856b"><?}?>



                                               <td class="text-center"><?=$cnt?></td>
                                                <td class="text-center"> <?if($data[state]!="정상"){?><font color="red" weight="bold"> <?=$data[state]?></font> <?}else{ echo "$data[state]";  }?></td>
                                                <td class="text-center"><?=$data[newtype]?></td>
                                                <td class="text-center">
                                                     <?=$data[end_type]?>
                                                     <? if($data[end_type]=="중도해지" or $data[end_type]=="중도상환"){
                                                          echo "<br>".$data[stop_date]; }
                                                          ?>
                                                     </td>

                                                    <td class="text-center"><?=$data[ctr_no]?></td>
                                                    <td class="text-center"><?=$data[name]?></td>
                                                    <td class="text-center"><?=$data[type]?></td>


                                                    <td class="text-right"><b><?=number_format($data[money])?>원</b></td>
                                                    <td class="text-center"><?=$data[ctr_start]?> ~ <?=$data[ctr_end]?></td>

                                                    <td class="text-center"><?=$data[rate_cus]?>%</td>
                                                    <td class="text-center"><?=$data[rate_manager]?>%</td>
                                                    <td class="text-center"><?=$data[rate_incentive]?>%</td>
                                                    <td class="text-center"><?=$data[rate_top]?>%</td>

                                                    <td class="text-center"><?=$data[id_manager]?></td>
                                                    <td class="text-center"><?=$data[id_incentive]?></td>
                                                    <td class="text-center"><?=$data[id_top]?></td>
                                                    <td class="text-center" style="font-size:5pt;"><?=number_format($data[id_top_volume]/1000)?>K</td>

<!-- 만약 재계약일경우 (예전계약과 현재계약과의 증감차이)/예전계약 표시해주게 -->
                                                    <td  class="text-center" style="font-size:1pt;">
                                                    <? if($data[newtype]=="재계약(LH)"){
                                                         $diff=($data[money]-$data[money_old]);
                                                         echo "<font color='red'>".number_format($diff/10000)."만/</font>".number_format($data[money_old]/10000)."만"; }
                                                       ?>
                                                      </td>

                                                 <td  class="text-center""><input type="button" name="view3" value="<?=$data[ctr_old]?>" ctr_old="<?=$data[ctr_old]?>" class="btn bg-gray btn-xs view_data3" style="font-size:1pt;"/> </td>

                                                 <td class="text-center "><b><?=number_format($data[end_back])?></b></td>
                                                 <td class="text-center"><?=number_format($data[end_add])?></td>
                                                 <td class="text-center "><?=number_format($data[end_remoney])?></td>

                                   <!-- 고객 이자지급 조회 버튼-->
                                                <td class="text-center"> <input type="button" name="view2" value="조회" id="<?=$data[no]?>" class="btn btn-xs view_data2 <?if($data[state]=="정상"){?> btn-primary"><?}else{?>  btn-inverse"><?}?></td>


                         <!-- 계약서수정 버튼  : 만약 정상이면 수정버튼이 뜨지만, 종료이면 종료방식(만기상환,재계약등등)이 표시된다. -->
                                                <td class="text-center">

                                               <a href="../admin/input_contract_edit.php?no=<?=$data[no]?>&ctr_no=<?=$data[ctr_no]?>"> <button type="button"  <?if($data[state]=="정상"){?> class="btn-xs btn-danger "><?}else{?>  class="btn-xs btn-inverse"><?}?> 수정</button></a>

                                               </td>

                                           </tr>
                                            <?
                                            $cnt++;
                                            }	?>

                                             <!-- /////////게시물 게시판 출력끝 -->

                                            <?  //페이징을 설정하면 한페이지 단위로만 합계가 계산되므로 전체 출력에 대한 합계를 별도로 구해야 한다.
                                            //따라서 $query_t를 둔 것이다.
                                            $total_sum=0;
                                            $total_int=0; $total_back=0; $total_remoney=0; $total_add=0;
                                            $tnt=0;
                                            $result_t=mysql_query($query_t,$connect);
                                            while($data_t = mysql_fetch_array($result_t)){
                                            $total_sum+=$data_t[money];
                                            $total_back+=$data_t[end_back];
                                            $total_remoney+=$data_t[end_remoney];
                                            $total_add+=$data_t[end_add];
                                            $total_int+=$data_t[sum_cus];

                                            $tnt++;
                                            }
                                            ?>

                                            <!--total cell-->
                                            <tr class="td-left2" style="font-size:11px;">
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center">총 계약건수 :</td>
                                                <td class="text-center text-danger"><?=$tnt?>건</td>
                                                <td class="text-center"></td>
                                                <td class="text-right">계약총액 :</td>
                                                <td class="text-center text-danger"><?=number_format($total_sum)?>원정</td>
                                                <td class="text-center text-danger">이자합:<?=number_format($total_int)?></td>
                                                <td class="text-right"></td>
                                                <td class="text-center "></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>

                                                <td class="text-right">상환총액 :</td>
                                                 <td class="text-center"></td>
                                                <td class="text-center text-danger"><?=number_format($total_back)?></td>
                                                <td class="text-center text-danger"><?=number_format($total_add)?></td>
                                                <td class="text-center text-danger"><?=number_format($total_remoney)?></td>
                                                <td class="text-center" colspan="10"></td>
                                            </tr>


                                            <tr style="font-size:14px;">
                                                <td colspan="30" class="text-center"><?include ('../member/list_page.php');?></td>
                                            </tr>


                                            </tbody>
                                    </table>

                                </div>
                          </div>
                      </div>

                </div>
               </div>
           </div>

</div>
</div>
</body>


<!-- footer -->
<? include('../_footer.php'); ?>

</html>

<? include('./modal_script.php'); //javascript 모달창 출력 ?>
