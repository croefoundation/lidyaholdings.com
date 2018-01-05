<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외
include('../_header.php');

$no= $_GET[no];

//1. (구)계약테이블을 조회해서 우선 필요한 정보를 불러온다..-----------
$query_r= "select * from contract where no='$no'";
mysql_query("set names ust8", $connect);
$result_r= mysql_query($query_r, $connect);
while($data = mysql_fetch_array($result_r)){ //while문 시작 계약서를 하나둘씩 불러와서..수당테이블에 저장한다.--------------------------------

$id= $data[id];
$newtype=$data[end_type]; //계약은 재계약으로
$ctr_old=$data[ctr_no];//기존 계약이 구계약번호로 저장

$type= $data[type]; //같은조건으로 계약유형을 1년만기 등으로 할건지

$name= $data[name];
$user_id=$data[user_id]; //유저아이디
$id_manager=$data[id_manager];
$id_incentive=$data[id_incentive];
$id_top=$data[id_top];

$money=$data[end_remoney];   //약정금액은 재계약금액으로
$money_old= $data[money]-$data[end_back]; //기존원금-찾아간것
$money_new= $data[end_add];
$ctr_start=$data[ctr_end];
$ctr_end_now= $data[ctr_end]; //종료일 설정-------

//종료일 정하기
$date=$ctr_end_now;
$date_y=substr($date,0,4);
$date_m=substr($date,5,2);
$date_d=substr($date,8,2);

$ctr_end=computeMonth($date_y,$date_m,$date_d,12);  //종료일 12개월후
$dy=substr($date,2,2);


//계약번호 마지막 숫자를 파악하기 위해
$query_count = "select count(*) from contract where id='ctr'";
mysql_query("set names utf8");  //언어셋 utf8
$result1= mysql_query($query_count, $connect);
$temp= mysql_fetch_array($result1);
$totals= $temp[0]+1;
$ctr_no= "LD0".$totals; ///계약번호 반드시 새로지정

$ctr_date=$data[ctr_end];

//지급율
$rate_cus= $data[rate_cus];
$rate_manager= $data[rate_manager];
$rate_incentive= $data[rate_incentive];
$rate_top= $data[rate_top];

}

?>


<body>
    <!--Contract Info-->
    <div class="p-y-0 " style="background-color:#">
        <div class="wraper container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title text-danger" style="font-size:20px"><b>[<?=$name?>]님 연장계약입력(관리자용)</b></h4>
                            <h6 class="text-primary"><b>**기존계약 내용에 따라 자동생성되었습니다. <br>***변경여부를 반드시 확인하세요</b></h6>
                        </div>
                        <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="font-size:13px;">

                                            <tbody>

<!-- 폼은 신규입력 포스트로 한다. -->
<form action="../admin/input_contract_post.php" method="post"  name='form_1'>

    <input type="hidden" name="id" value="ctr">

    <tr>
         <td class="td-left" style="padding-left:20px;">계약종류 </td>
         <td>
              <div class="radio-inline">
                 <label class="cr-styled">
                     <input type="radio" name="newtype" value="신규">
                     <i class="fa"></i>
                     신규계약
                 </label>
              </div>
            <div class="radio-inline">
                 <label class="cr-styled">
                     <input type="radio" name="newtype" value="재계약(LH)"
<?if($newtype=='재계약(LH)'){?>checked=""<?}?>>
                     <i class="fa"></i>
                     재계약(리디아)
                 </label>
            </div>

            <div class="radio-inline">
                 <label class="cr-styled">
                    <input type="radio" name="newtype" value="재계약(DL)"
                    <?if($newtype=='재계약(DL)'){?>checked=""<?}?>>
                    <i class="fa"></i>
                    재계약(딜라이트)
                 </label>
            </div>

         </td>
     </tr>


     <tr>

      <td class="td-left bg-danger" style="padding-left:20px;">1.계약번호**</td>
      <td class="bg-warning"> <input type=text name=ctr_no class="input-sm text-danger" size=20 required='' value="<?=$ctr_no?>">
          <!------- 계약번호 중복확인 점검 ----------------------->
          <input type=button value='자동생성됨'  class="btn btn-danger btn-xs" onclick="ctr_check();">
          <font color='blue';>* (계약번호는 자동생성되었습니다. !!!)</font></td>

     <tr>


     <tr>
         <td class="td-left " style="padding-left:20px;">계약자 성명*</td>
         <td> <input type=text name=name class="input-sm text-danger" size=20 required='' value="<?=$name?>" readonly="">

              <font color=blue>* (성명은 기존계약과 동일합니다. )</font></td>
     </tr>


      <tr>
          <td class="td-left1  bg-danger" style="padding-left:20px;">2.계약종류 수정*</td>
          <td class="bg-success">
              <select name="type" class="form-control" required="">

                  <option value="1년만기/만기지급"
                   <?if($type=='1년만기/만기지급'){?> class="text-danger" selected=""<?}?>
                  >[Type1] 1년만기형(만기지급-연01회이자지급)</option>

                  <option value="1년만기/반기지급"
                   <?if($type=='1년만기/반기지급'){?> class="text-danger" selected="" <?}?>
                  >[Type2] 1년만기형(반기지급-연02회이자지급)</option>

                  <option value="1년만기/분기지급"
                   <?if($type=='1년만기/분기지급'){?> class="text-danger" selected=""<?}?>
                  >[Type3] 1년만기형(분기지급-연04회이자지급)</option>

                  <option value="1년만기/매월지급"
                   <?if($type=='1년만기/매월지급'){?> class="text-danger" selected=""<?}?>
                  >[Type4] 1년만기형(매월지급-연12회이자지급)</option>

                  <option value="6개월만기/만기지급"
                   <?if($type=='6개월만기/만기지급'){?>class="text-danger" selected=""<?}?>
                  >[Type5] 6개월만기형(만기시 1회이자지급)</option>

                  <option value="월대차/만기지급"
                   <?if($type=='월대차/만기지급'){?>class="text-danger" selected=""<?}?>
                  >[Type6] 월대차형(월납입 만기시 이자지급)</option>
              </select>
              <h6 class="text-primary"><b>**재계약시 계약종류- 변경여부를 반드시 확인하세요</b></h6>

          </td>
      </tr>


      <tr>
      <td rowspan="2" class="td-left1 text-danger" style="padding-left:20px;">재계약 금액*</td>
      <td>
          <span>
              합계 <input type="number" name="money" class="text-danger input-sm" min="0" placeholder=" " value="<?=$money?>" required="">
              <label class="btn btn-danger btn-xs"> 원정</label>
          </span>
          <font color=blue>* (재계약 금액은 이전화면에서 입력했기에, 다시한번 정확히 확인해 주세요.</font>
      </td>
      </tr>

     <tr>

         <td>
            <b>재계약일 경우</b> = 이월금액 <input type="text" name="money_old" class="input-sm" value="<?=$money_old?>"> + 신규금액<input type="text" name="money_new" class="input-sm" value="<?=$money_new?>" >
         </td>
     </tr>

     <tr>
         <td class="td-left1 bg-danger" style="padding-left:20px;">3.계약기간*</td>
         <td class="bg-info">
          <input type="date" name="ctr_start" class="input-sm text-danger" value="<?=$ctr_start?>" required="">~<input type="date" name="ctr_end" class="input-sm text-danger" required="" value="<?=$ctr_end?>">
          <font color=blue>* (자동생성: 구계약 종료일로 부터 +1년이므로, 계약종류가 변경시 다시한번 잘 확인하시기 바랍니다.)</font>
         </td>
     </tr>


     <tr>
         <td class="td_left1 bg-danger" style="padding-left:20px;">4.이율 및 지급율*</td>
         <td class="bg-warning td-left">
             <h6 class="text-primary"><b>**재계약시 이율변동- 변경여부를 반드시 확인하세요</b></h6>

          (1)고객이율 : <input type="text" name="rate_cus" class="input-sm text-danger" value="<?=$rate_cus?>" text-danger" size="2" required=""> %
          &nbsp; &nbsp; &nbsp;
          (2)팀장 커미션 : <input type="text" name="rate_manager" class="input-sm text-danger" value="<?=$rate_manager?>" text-danger" size="2" required=""> %
          &nbsp; &nbsp; &nbsp;
          (3)팀장 인센티브 : <input type="text" name="rate_incentive" class="input-sm text-danger" value="<?=$rate_incentive?>" text-danger" size="2" required=""> %
          &nbsp; &nbsp; &nbsp;
          (4)본부장 성과급 : <input type="text" name="rate_top" class="input-sm text-danger" value="<?=$rate_top?>" text-danger" size="2" required=""> %
          <br><br><font color="red" size="2pt">

               *1년만기(만기지급,반기지급)외에, ==>분기지급,매월지급,6개월형,월대차는 인센티브,본부장수수료가 지급 되지않으므로 "0"을 입력하세요. <br>만약,별도 조건일경우는 지급율을 직접 입력하세요)</font>

         </td>
     </tr>

     <tr>
         <td class="td-left1 text-danger" style="padding-left:20px;">담당팀장</td>
         <td>
          <input type="text" name="id_manager" class="input-sm text-danger" value="<?=$id_manager?>" value="" required="" readonly="">

          &nbsp; &nbsp; /// 소개팀장 : <input type="text" name="id_incentive" class="input-sm text-danger" value="<?=$id_incentive?>" value="" required="" readonly="">

         &nbsp; &nbsp; 본부장:
          <input type="text" name="id_top" class="input-sm text-danger" value="<?=$id_top?>" value="" required="" readonly="">
         </td>
     </tr>
     <tr>
         <td class="td-left" style="padding-left:20px;">계약일</td>
         <td>
          <input type="date" name="ctr_date" class="input-sm text-danger" value="<?=$ctr_date?>" required="">
         </td>
     </tr>

     <tr>
        <td class="td-left" style="padding-left:20px;">구 계약번호</td>
        <td>
          재계약일경우 : 구계약번호 <input type="text" name="ctr_old" class="input-sm text-danger" value="<?=$ctr_old?>" >
        </td>
     </tr>

     <tr>
      <td class="td-left" style="padding-left:20px;">메모</td>
      <td>
            <textarea rows="3"  name="memo" id="memo" class="form-control text-danger" value="<?=$memo?>" placeholder=""></textarea>
      </td>
     </tr>


    </tbody>

 </table>




                                    </div>
                                    <div class="text-center" style="padding-top:20px;">
                                        <input type="submit"  class="btn btn-danger" value="재계약서 등록하기" >
                                        <!--onclick="return confirm('입력한 값으로 등록하시겠습니까?')" -->
                                         &nbsp;&nbsp;
                                        <input type="reset"  class="btn btn-default" value="수정전 입력값" onclick="location.reload();">
                                        <input type="reset"  class="btn btn-default" value="나중에 수동으로 등록하기" onclick="location.href='../member/list_ctr.php';">
                                    </div>
                               </form>
                        </div> <!--pannel body end  -->
                    </div>
                </div>
            </div> <!-- End row -->





      </div> <!--end wraper-->
    </div> <!--end contents-->


            <!-- footer -->
            <? include('../_footer.php'); ?>

            </html>

            <!--END--Footer---------------->
