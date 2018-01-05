<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외

?>
<script>
window.alert('기존 수당테이블을 삭제하고 마감을 새로 돌립니까?');
</script>

<?

//*********************************************************************************************************************
// 로직명 : 홀딩스 마감돌리기
///////////////////////////////////////////////////////////////////
//   1. 기존 수당테이블 삭제
//   2. 실적 재계산 - 계약서를 날짜순으로 읽어서 상위실적을 계산해서 ===><회원테이블에 저장>
//      --total_siljuk_ctr_mem.php 인클루드해서 <본부장 정하기> ===> 계약서테이블에 저장
//   3. 마감돌리기 (계약서 날짜 순서대로 불러와서 각자 계산하기 ===>수당테이블에 저장
//   4.
//   5.
//   6. 수당 다 돌리고...나서 대쉬보드 업데이트하기..

//홀딩스 마감돌리기****************************************************************************************************


//[1].수당테이블을 싹 지운다.. 다시 저장하기 위해서..
//주의***모든 컨텐츠 삭제..

$sql="TRUNCATE TABLE payment" ;
mysql_query($sql,$connect);
mysql_close;


echo "수당테이블을 삭제하였습니다.<br>";

//[2] 사전 준비
//실적초기화: 0, 본부장세팅초기화:회사 ,
//계약서 불러와서 다시 계산할것.. 본부장 확정


// include('../lib/fn_siljuk.php'); //시간,날짜변환외

echo "실적과 본부장을 재 계산 하였습니다.<br>";



//[2부] 수당 마감 돌리기 --------------------------
//(1)완성된 계약테이블을 조회해서 우선 불러온다..-----------


$query_r= "select * from contract where id='ctr' order by ctr_start asc";
mysql_query("set names ust8", $connect);
$result_r= mysql_query($query_r, $connect);
while($data = mysql_fetch_array($result_r)){ //while문 시작---- 계약서를 하나씩 불러와서..데이터를 읽고,,,수당을 계산한다..--------------------------------

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



//(2)계약에 따라 지급율을 정하고 수당마감을 계약종류별로 진행한다. 그리고 payment 수당테이블에 저장한다.
switch ($type) {
     case '1년만기/만기지급':
          include('../admin/ctr_1yman.php');
          break;

     case '1년만기/반기지급':
          include('../admin/ctr_1yban.php');
          break;

     case '1년만기/분기지급':
          include('../admin/ctr_1ybun.php');
          break;

     case '1년만기/매월지급':
          include('../admin/ctr_1ymon.php');
          break;

     case '6개월만기/만기지급':
          include('../admin/ctr_6m.php');
          break;

     case '월대차/만기지급':
          include('../admin/ctr_month.php');
          break;
} //계약종류 선택 종료


} //WHILE문종료--계약서 불러와서 하나씩 체크해서 마감돌리기종료

echo "마감을 돌렸구요.<br>";

//수당계산이 완료되면.. 전체회원들을 돌려서..데쉬보드를 업데이드 한다...수당테이블에서 회원테이블로 데이터 통계저장
$query="select * from member where id='mem'";
$result=mysql_query($query,$connect);
while($member = mysql_fetch_array($result)){
include('../admin/dashboard.php');
}

echo "데쉬보드를 업데이트 햇습니다..<br>";

//수당테이블의 번호를 보기좋게 정리해준다.****

$sql="ALTER TABLE payment AUTO_INCREMENT=1" ;
mysql_query($sql,$connect);

$sql="SET @COUNT = 0 ";
mysql_query($sql,$connect);

$sql="UPDATE `payment` SET `payment`.`no` = @COUNT:=@COUNT+1 ";
mysql_query($sql,$connect);

//맨마지막을 시작값을 다시 세팅함
$query_count ="select count(*) from payment where id='pay'"; //수당테이블 전체 갯수세기
$result1= mysql_query($query_count, $connect);
$temp= mysql_fetch_array($result1);
$totals= $temp[0];

$new=$totals+1;
$sql="ALTER TABLE payment AUTO_INCREMENT=$new" ;
mysql_query($sql,$connect);

echo "수당테이블 번호등을 정리하여씁니다...<br>";

?>


   <script>
   window.alert('계약정보를 불러와서 첫거래부터 마감을 돌려서 수당을 다시 계산하였습니다.');
   location.href='../member/list.php';
   </script>
