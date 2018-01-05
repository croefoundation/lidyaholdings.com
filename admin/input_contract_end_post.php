<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외

$no= $_POST[no];
$ctr_no=$_POST[ctr_no];
$money=$_POST[money]; //약정금 불러와서

$end_type=$_POST[end_type];
$end_back=$_POST[end_back];
$end_add=$_POST[end_add];
$end_remoney=$_POST[end_remoney];
$memo=$_POST[memo];


if(!$end_type)Error("1.종료할 형태를 선택하세요");
if($end_type=="만기상환"){if(!$end_back)Error("2.만기상환 할때는 최종 상환할 금액을 정확히 입력하세요");};
if($end_type=="만기상환"){if($end_back!=$money)error("만기상환 종료는 상환금액이 원금과 같아야 합니다.");};
if($end_type!="만기상환"){
     if(!$end_remoney)Error("재계약일경우=>(3)재계약할 금액을 정확히 입력하세요");
     $remoney=$money-$end_back+$end_add;
    if($end_remoney!=$remoney)Error("재계약 최종금액= 기존원금-부분원금상환 + 추가금액  ==>(3)재계약할 금액을 정확히 입력하세요");
} //재계약일 경우 에러처리


$state="종료";


//종료할때~~(기존계약업데이트) 계약구분-종료로 표시하고,상태는 "종료"",
                     //수정버튼은 :종료처리로 바꿔놔
//재계약일때는~~(신규신청 insert) 계약구분-재계약LH,약정금액=end_remoney,고객이율,팀장,약정기간, ...증감현황, 현재와차이/과거표시, 상태 :정상
//            no,ctr_no로 계약서를 불러와서..수정할 부분만 수정할 수 있도록 하고......이후는-->신규등록하는것과 같다.


//1.계약종료--계약서 DB를 전체 업데이트한다.
$query="update contract set


state='$state',

memo='$memo',
end_type='$end_type',
end_back='$end_back',
end_add='$end_add',
end_remoney='$end_remoney'

where no='$no' ";

mysql_query("set names utf8", $connect);
mysql_query($query, $connect);
mysql_close;



//회원등록,수당계산이 완료되면.. 전체회원들을 돌려서..데쉬보드를 업데이드 한다...수당테이블에서 회원테이블로 데이터 통계저장
$query="select * from member where id='mem'";
$result=mysql_query($query,$connect);
while($member = mysql_fetch_array($result)){
include('../admin/dashboard.php');
}

?>



<? if($end_type=="만기상환"){

//이미 처리했다면 중복 처리 하지않기
     $query_p= "select * from payment where ctr_no='$ctr_no' and pay_type='만기상환금'";
     mysql_query("set names ust8", $connect);
     $result_p= mysql_query($query_p, $connect);
     $dap = mysql_fetch_array($result_p);
     if($dap[pay_type]=="만기상환금")Error("이미 상환처리하였습니다.");


     //마지막 상환할 금액을 수당으로 찍어놓는다.....

          $query_r= "select * from contract where ctr_no='$ctr_no'";
          mysql_query("set names ust8", $connect);
          $result_r= mysql_query($query_r, $connect);
          $data = mysql_fetch_array($result_r);  //유저 아이디 찾기위해서....

          $name=$data[name];//계약자 이름
          $user_id=$data[user_id]; //유저아이디
          $pay_name= '고객 만기상환금액';
          $pay_type= '만기상환금';
          $pay_date= $data[ctr_end];
          $amount= $data[money];
          $period="일시지급";
          $ctr_name=$data[name];
          $comment="원금:".$data[money]."+이자지급분:".$data[sum_cus];
          $pay_state="만기상환";


          //DB입력
          $query="INSERT INTO payment (name, user_id, pay_name, pay_type, pay_date, amount, period, ctr_no, ctr_name, comment, pay_state, stop_date)
                 VALUES ('$name', '$user_id', '$pay_name', '$pay_type', '$pay_date', '$amount', '$period', '$ctr_no', '$ctr_name', '$comment', '$pay_state','$stop_date')";
          mysql_query("set names utf8", $connect);
          mysql_query($query, $connect);
          mysql_close; //끝내기.

?>



<script>
window.alert('계약이 만기상환 되어 종료 처리되었습니다.');
location.href='../member/list_ctr.php';
</script>
<?}?>


<? if($end_type=="재계약(DL)"){ ?>
     <script>
     window.alert('재계약[<?=$end_type?>]으로 처리되었습니다.');
     location.href='../member/list_ctr.php';
     </script>
<?}?>



<? if($end_type=="재계약(LH)"){
//자동계약서 작성 처리여부
      ?>

     <script>
     window.alert('재계약[<?=$end_type?>]으로 처리되었습니다. \n 재계약시 변경되는 계약번호, 이자율 등, 재계약조건을 입력하기 위한 <재계약 입력폼>으로 이동합니다.');
     location.href='../admin/input_contract_end_again.php?no=<?=$no?>';
     </script>

<?}?>
