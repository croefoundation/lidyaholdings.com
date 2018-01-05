<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보

if($member[user_id]!="admin")Error_member("관리자용 페이지입니다.");
include('../lib/lib.php'); //시간,날짜변환외


//홀딩스 마감돌리기****************************************************************************************************

//계약서를 불러와서 처음부터 하나씩.. 계약자를 찾으면,,

//1.회원등록 읽어서 팀장,추천인, 본부장 읽어오기
//2.계약서에서 계약이율대로 이자총괄 각자 계산하기
//3.계약순서대로 순차적으로 읽어서 본부장 정하기...계약서에 새로 입력..해서 마감 돌리기전에 정리할것
//4. 수당 뿌리기 전에 데이타베이스 정리...
//5. 수당 다 돌리고...나서 대쉬보드 업데이트하기..




//계약테이블을 조회해서 우선 불러온다..-----------
//    1.약정기간이 먼저인 순서로 소팅한다--본부장 정하게여..

$query_r= "select * from contract where id='ctr' order by ctr_start asc";
mysql_query("set names ust8", $connect);
$result_r= mysql_query($query_r, $connect);
while($data = mysql_fetch_array($result_r)){ //while문 시작 계약서를 하나둘씩 불러와서..수당테이블에 저장한다.--------------------------------

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

echo $ctr_start."<br>";
}
?>
