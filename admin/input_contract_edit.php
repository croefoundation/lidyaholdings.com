<? header("content-type:text/html; charset=UTF-8"); ob_start;?>
<html>

<?
include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자 메뉴입니다.");
//include('./contract_type_rate.php');//이자율지정-리디아홀딩스는 개별입력

$no=$_GET[no];
$ctr_no=$_GET[ctr_no];

$queryi="select * from contract where no='$no' and ctr_no='$ctr_no'";
mysql_query("set names UTF8",$connect);
$resulti=mysql_query($queryi,$connect);
$info=mysql_fetch_array($resulti);
?>

<!-- Top menu -->
<? include('../_header.php'); ?>

<script>

/*등록확인 체크란*/
 function id_check(){
     if(!document.form_1.name.value){
       window.alert('등록고객명을 입력하셔야 합니다.');
       document.form_1.name.focus();
       return false;
     }
     var a = document.form_1.name.value;

     window.open('../admin/id_check.php?name='+a,'','width=300, height=100');
     }

     /*계약번호 체크란*/
      function ctr_check(){
          if(!document.form_1.ctr_no.value){
            window.alert('계약번호를 먼저 입력하셔야 합니다.');
            document.form_1.ctr_no.focus();
            return false;
          }
          var b = document.form_1.ctr_no.value;

          window.open('../admin/ctr_check.php?ctr_no='+b,'','width=300, height=100');
          }

</script>


<body>
    <!--Contract Info-->
    <div class="p-y-0 " style="background-color:#">
        <div class="wraper container">

 <!-- ///////////////////////////////////////////////////////////// 세팅중에 다 나타나게.//계약수정은 정상인경우, 종료수정은 종료된것만-->


 <?if($info[state]=="정상"){  // 정상인경우만 수정할수 있게 한다. ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title text-danger" style="font-size:25px"><b>1.계약서 수정(관리자용)</b></h2>
                            <h4>*계약의 종료처리, 재계약여부는 하단의 2.계약종료 처리를 이용하세요..</h4>
                        </div>
                        <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="font-size:13px;">

                                            <tbody>

                                       <form action="../admin/input_contract_edit_post.php" method="post"  name='form_1'>
                                           <input type="hidden" name="no" value="<?=$no?>">
                                           <input type="hidden" name="id" value="ctr">
                                           <input type="hidden" name="mode" value="수정">

                                           <tr>
                                                <td class="td-left" style="padding-left:20px;">계약상태 </td>
                                                <td>
                                                     <div class="radio-inline">
                                                       <label class="cr-styled">
                                                             <input type="radio" name="state" value="정상" <?if($info[state]=='정상'){?>checked=""<?}?>>
                                                             <i class="fa"></i>
                                                             정상유지중
                                                       </label>
                                                     </div>
                                                    <div class="radio-inline">
                                                       <label class="cr-styled">
                                                            <input type="radio" name="state" value="종료"
                                                            <?if($info[state]=='종료'){?>checked=""<?}?>>
                                                            <i class="fa"></i>
                                                            종료된 계약
                                                       </label>
                                                    </div>
                                                </td>
                                            </tr>

                                           <tr>
                                                <td class="td-left" style="padding-left:20px;">계약종류(매출) </td>
                                                <td>
                                                     <div class="radio-inline">
                                                        <label class="cr-styled">
                                                             <input type="radio" name="newtype" value="신규" <?if($info[newtype]=='신규'){?>checked=""<?}?>>
                                                             <i class="fa"></i>
                                                             신규계약
                                                        </label>
                                                     </div>
                                                    <div class="radio-inline">
                                                        <label class="cr-styled">
                                                            <input type="radio" name="newtype" value="재계약(LH)"
                                                            <?if($info[newtype]=='재계약(LH)'){?>checked=""<?}?>>
                                                            <i class="fa"></i>
                                                            재계약(리디아)
                                                        </label>
                                                    </div>

                                                    <div class="radio-inline">
                                                        <label class="cr-styled">
                                                           <input type="radio" name="newtype" value="재계약(DL)"
                                                           <?if($info[newtype]=='재계약(DL)'){?>checked=""<?}?>>
                                                           <i class="fa"></i>
                                                           재계약(딜라이트)
                                                        </label>
                                                    </div>

                                                </td>
                                            </tr>


                                            <tr>
                                             <input type="hidden" name="ctr_no_old" value="<?=$info[ctr_no]?>">
                                              <td class="td-left" style="padding-left:20px;">계약번호*</td>
                                              <td> <input type=text name=ctr_no class="input-sm text-danger" size=20 required='' value="<?=$info[ctr_no]?>">
                                                   <!------- 계약번호 중복확인 점검 ----------------------->
                                                  <input type=button value='중복확인'  class="btn btn-danger btn-xs" onclick="ctr_check();">
                                                   <font color='blue';>* (계약번호수정시만 중복여부 확인하세요.!!!)</font></td>

                                            <tr>


                                            <tr>
                                                <td class="td-left " style="padding-left:20px;">계약자 성명*</td>
                                                <td> <input type=text name=name class="input-sm text-danger" size=20 required='' value="<?=$info[name]?>">
                                                     <!------- 계약자 등록확인 점검 ----------------------->
                                                    <input type=button value='등록확인'  class="btn btn-danger btn-xs" onclick="id_check();">
                                                     <font color=blue>* (성명수정시 : 동명이인 여부도 확인바랍니다.)</font></td>
                                            </tr>


                                             <tr>
                                                 <td class="td-left1 text-danger" style="padding-left:20px;">계약종류 수정*</td>
                                                 <td>
                                                     <select name="type" class="form-control" required="">

                                                         <option value="1년만기/만기지급"
                                                          <?if($info[type]=='1년만기/만기지급'){?> class="text-danger" selected=""<?}?>
                                                         >[Type1] 1년만기형(만기지급-연01회이자지급)</option>

                                                         <option value="1년만기/반기지급"
                                                          <?if($info[type]=='1년만기/반기지급'){?> class="text-danger" selected="" <?}?>
                                                         >[Type2] 1년만기형(반기지급-연02회이자지급)</option>

                                                         <option value="1년만기/분기지급"
                                                          <?if($info[type]=='1년만기/분기지급'){?> class="text-danger" selected=""<?}?>
                                                         >[Type3] 1년만기형(분기지급-연04회이자지급)</option>

                                                         <option value="1년만기/매월지급"
                                                          <?if($info[type]=='1년만기/매월지급'){?> class="text-danger" selected=""<?}?>
                                                         >[Type4] 1년만기형(매월지급-연12회이자지급)</option>

                                                         <option value="6개월만기/만기지급"
                                                          <?if($info[type]=='6개월만기/만기지급'){?>class="text-danger" selected=""<?}?>
                                                         >[Type5] 6개월만기형(만기시 1회이자지급)</option>

                                                         <option value="월대차/만기지급"
                                                          <?if($info[type]=='월대차/만기지급'){?>class="text-danger" selected=""<?}?>
                                                         >[Type6] 월대차형(월납입 만기시 이자지급)</option>
                                                     </select>
                                                 </td>
                                             </tr>


                                             <tr>
                                             <td rowspan="2" class="td-left1 text-danger" style="padding-left:20px;">약정금액*</td>
                                             <td>
                                                 <span>
                                                     합계 <input type="number" name="money" class="text-danger input-sm" min="0" placeholder=" " value="<?=$info[money]?>" required="">
                                                     <label class="btn btn-danger btn-xs"> 원정</label>
                                                 </span>
                                                  <font color=blue>* (금액수정시 : 쉼표없이 입력바랍니다.)</font>
                                             </td>
                                             </tr>

                                            <tr>

                                                <td>
                                                    <b>재계약일 경우</b> = 이월금액 <input type="text" name="money_old" class="input-sm" value="<?=$info[money_old]?>"> + 신규금액<input type="text" name="money_new" class="input-sm" value="<?=$info[money_new]?>" >
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="td-left1 text-danger" style="padding-left:20px;">계약기간*</td>
                                                <td>
                                                   <input type="date" name="ctr_start" class="input-sm text-danger" value="<?=$info[ctr_start]?>" required="">~<input type="date" name="ctr_end" class="input-sm text-danger" required="" value="<?=$info[ctr_end]?>">
                                                   <font color=blue>* (종료일을 잘 확인하시기 바랍니다.)</font>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td class="bg-danger" style="padding-left:20px;">이율 및 지급율*</td>
                                                <td class="bg-warning td-left">
                                                  (1)고객이율 : <input type="text" name="rate_cus" class="input-sm text-danger" value="<?=$info[rate_cus]?>" text-danger" size="2" required=""> %
                                                  &nbsp; &nbsp; &nbsp;
                                                  (2)팀장 커미션 : <input type="text" name="rate_manager" class="input-sm text-danger" value="<?=$info[rate_manager]?>" text-danger" size="2" required=""> %
                                                  &nbsp; &nbsp; &nbsp;
                                                  (3)팀장 인센티브 : <input type="text" name="rate_incentive" class="input-sm text-danger" value="<?=$info[rate_incentive]?>" text-danger" size="2" > %
                                                  &nbsp; &nbsp; &nbsp;
                                                  (4)본부장 성과급 : <input type="text" name="rate_top" class="input-sm text-danger" value="<?=$info[rate_top]?>" text-danger" size="2" > %
                                                  <br><br><font color="red" size="1.8pt">*1년만기(만기지급,반기지급)외에, ==>분기지급,매월지급,6개월형,월대차는 인센티브,본부장수수료가 지급 되지않으므로 "0"을 입력하세요. <br>만약,별도 조건일경우는 지급율을 직접 입력하세요)</font>

                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="td-left1 text-danger" style="padding-left:20px;">담당팀장</td>
                                                <td>
                                                   <input type="text" name="id_manager" class="input-sm text-danger" value="<?=$info[id_manager]?>" value="" required="">

                                                  &nbsp; &nbsp; /// 소개팀장 : <input type="text" name="id_incentive" class="input-sm text-danger" value="<?=$info[id_incentive]?>" value="" required="">

                                                &nbsp; &nbsp; 본부장:
                                                   <input type="text" name="id_top" class="input-sm text-danger" value="<?=$info[id_top]?>" value="" required="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td-left" style="padding-left:20px;">계약일</td>
                                                <td>
                                                   <input type="date" name="ctr_date" class="input-sm text-danger" value="<?=$info[ctr_date]?>" required="">
                                                </td>
                                            </tr>

                                            <tr>
                                               <td class="td-left" style="padding-left:20px;">구 계약번호</td>
                                               <td>
                                                   재계약일경우 : 구계약번호 <input type="text" name="ctr_old" class="input-sm text-danger" value="<?=$info[ctr_old]?>" >
                                               </td>
                                            </tr>

                                            <tr>
                                              <td class="td-left" style="padding-left:20px;">메모</td>
                                              <td>
                                                    <textarea rows="3"  name="memo" id="memo" class="form-control text-danger" value="<?=$info[memo]?>" placeholder=""></textarea>
                                              </td>
                                            </tr>


                                           </tbody>

                                        </table>
                                    </div>
                                    <div class="text-center" style="padding-top:20px;">
                                        <input type="submit"  class="btn btn-danger" value="수정하기" >
                                        <!--onclick="return confirm('입력한 값으로 등록하시겠습니까?')" -->
                                         &nbsp;&nbsp;
                                        <input type="reset"  class="btn btn-default" value="수정전 입력값" onclick="location.reload();">
                                        <input type="reset"  class="btn btn-default" value="수정취소" onclick="location.href='../member/list_ctr.php';">
                                    </div>
                               </form>
                        </div> <!--pannel body end  -->
                    </div>
                </div>
            </div> <!-- End row -->

<?}?>




<!-- /////////////////////////////////////////////////////////////////////////////////////////////////
// 종료계약 처리 루틴
//
//
//////////////////////////////////////////////////////
 -->





 <?if($info[state]=="종료" or "정상"){    //정상이든 종료든 수정할 수 있게
      ?>


<!-- 계약종료 루틴 *******************-->


<div class="row ">
    <div class="col-md-12">
        <div class="panel panel-default ">
            <div class="panel-heading ">
               <h4 class="panel-title text-danger" style="font-size:25px"><b>계약종료 처리(관리자용)</b></h4>
            </div>

            <table class="table-bordered" style="background-color:white; font-size:12px; width:100%; padding:10px;">
            <tr>
               <td class="td-left" style="padding-left:20px;">계약번호</td>
               <td> 계약번호: <b class="text-danger"><font size="3">[ <?= $ctr_no ?> ]</font></b></td>
            </tr>
            <tr>
               <td class="td-left " style="padding-left:20px;">계약자 성명</td>
               <td style="font-size:14px; color:red;"><b> <?=$info[name]?></b> (<?=$info[user_id]?>)</td>
            </tr>

            <tr>
               <td class="td-left " style="padding-left:20px;">계약 내용</td>
               <td>
                   1.계약종류 : <b class="text-info"><?= $info[type] ?> </b><br>
                   2.계약금액 : <b class="text-info"><?= number_format($info[money]) ?> 원</b><br>
                   3.이자율(%): <b class="text-info"><?= $info[rate_cus] ?>(%) </b> <br>
                   4.관리팀장 : <b class="text-info"><?= $info[id_manager] ?> (<?= $info[rate_manager] ?> %) </b><br>
                   5.소개팀장 : <b class="text-info"><?= $info[id_incentive] ?>(<?= $info[rate_incentive] ?> %) </b><br>
                   6.본부장 : <b class="text-info"><?= $info[id_top] ?> (<?= $info[rate_top] ?> %) </b><br>
                   7.계약기간 : <b class="text-info"><?= $info[ctr_start] ?>~<?= $info[ctr_end] ?> </b><br>

               </td>
            </tr>

            <tr>
               <td class="td-left text-danger " style="padding-left:20px; width:20%">처리기준일*</td>
               <td><?= $info[ctr_end] ?>
            </tr>
       </table>



            <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="font-size:13px;">

                                <tbody>

<!-- 계약종료 Post는 별도의 input_contract_end_post.php로 보내서 처리한다. -->
                           <form action="../admin/input_contract_end_post.php" method="post"  name='form_2'>
                              <input type="hidden" name="no" value="<?=$no?>">
                              <input type="hidden" name="ctr_no" value="<?=$ctr_no?>">
                              <input type="hidden" name="money" value="<?=$info[money]?>">

                              <tr>
                                    <td class="td-left text-danger" style="padding-left:20px;">1.계약종료 종류 </td>
                                    <td class="td-left">
                                         <div class="radio-inline">
                                           <label class="cr-styled">
                                                <input type="radio" name="end_type" value="만기상환"
                                                <?if($info[end_type]=='만기상환'){?>checked=""<?}?>>
                                                <i class="fa"></i>
                                                종료(만기상환)
                                           </label>
                                         </div>
                                         <div class="radio-inline">
                                          <label class="cr-styled">
                                               <input type="radio" name="end_type" value="재계약(LH)"
                                               <?if($info[end_type]=='재계약(LH)'){?>checked=""<?}?>>
                                               <i class="fa"></i>
                                               재계약(리디아홀딩스)
                                          </label>
                                        </div>
                                        <div class="radio-inline">
                                          <label class="cr-styled">
                                               <input type="radio" name="end_type" value="재계약(DL)"
                                              <?if($info[end_type]=='재계약(DL)'){?>checked=""<?}?>>
                                               <i class="fa"></i>
                                               재계약(딜라이트)
                                          </label>
                                        </div>
                                    </td>
                                </tr>

                                 <tr>
                                 <td class="td-left2 text-danger" style="padding-left:20px;"><h4>2.원금상환액*<h4></td>
                                 <td class="td-left1">
                                     <span>
                                         원금( <b style="color:red; font-size:14pt"><?=number_format($info[money])?></b>)중 <br>상환 금액 : <input type="number" name="end_back" class="text-danger input-sm" min="0"  <?if($info[end_type]=='만기상환'){?>vlaue="<?=number_format($info[money])?>"<?}?>>
                                         <label class="btn btn-primary btn-xs"> 원정</label>
                                    </span> <br><br>
                                      <font color=blue; size="">*만기에 전액상환시==>:원금전액을 입력 <br> *부분상환시 ==>부분상환한 금액을 입력 (쉼표없이 입력)</font>
                                 </td>
                                 </tr>

                                 <tr>
                                 <td class="td-left2 bg-gray" style="padding-left:20px;"> (재계약시)추가금액</td>
                                 <td class="td-left2 bg-gray">
                                     <span>
                                        <input type="number" name="end_add" class="text-danger input-sm" min="0" placeholder=" " value="" >
                                         <label class="btn btn-primary btn-xs"> 원정</label>
                                     </span>

                                 </td>
                                 </tr>

                                 <tr>
                                 <td class="td-left1 bg-danger text-inverse" style="padding-left:20px;">(재계약시)<br><h4>3.재계약할 총액*</h4></td>
                                 <td class="bg-danger text-inverse">
                                     <span>
                                         <input type="number" name="end_remoney" class="text-danger input-sm" min="0" placeholder=" " value="" >
                                         <label class="btn btn-primary btn-xs"> 원정</label>
                                    </span><br><br>
                                       <font color=white; size="2pt">*재계약금액은=원금-(지불금제외)+추가금을 합한 금액(쉼표없이 입력)<br>
                                            *종료된 계약은 입력하지 말것</font>
                                 </td>
                                 </tr>

                                <tr>
                                 <td class="td-left" style="padding-left:20px;">메모</td>
                                 <td class="td-left">
                                        <input type="text"  name="memo" id="memo" class="form-control text-danger" value="<?=$info[memo]?>" placeholder="">
                                 </td>
                                </tr>



                              </tbody>

                            </table>
                        </div>
                        <div class="text-center" style="padding-top:20px;">
                            <input type="submit"  class="btn btn-info" value="계약종료 처리 >>등록하기" >
                            <!--onclick="return confirm('입력한 값으로 등록하시겠습니까?')" -->
                            &nbsp;&nbsp;

                            <input type="reset"  class="btn btn-default" value="등록취소" onclick="location.href='../member/list_ctr.php';">
                        </div>
                   </form>
            </div> <!--pannel body end  -->
        </div>
    </div>
</div> <!-- End row -->

<?}?>



      </div> <!--end wraper-->
    </div> <!--end contents-->


            <!-- footer -->
            <? include('../_footer.php'); ?>

            </html>

            <!--END--Footer---------------->
