<? header("content-type:text/html; charset=UTF-8"); ob_start;

include('../lib/db_connect.php');
$connect=dbconn(); //DB컨넥트
$member=member();  //회원정보


// $ctr_no_old="D17210";
// $sql="DELETE FROM payment WHERE ctr_no='$ctr_no_old'" ;
// mysql_query($sql,$connect);

//수당테이블의 번호를 보기좋게 정리해준다.***************************

$sql="ALTER TABLE contract AUTO_INCREMENT=1" ;
mysql_query($sql,$connect);

$sql="SET @COUNT = 0 ";
mysql_query($sql,$connect);

$sql="UPDATE `contract` SET `contract`.`no` = @COUNT:=@COUNT+1 ";
mysql_query($sql,$connect);

//맨마지막을 시작값을 다시 세팅함
$query_count ="select count(*) from contract where id='pay'"; //수당테이블 전체 갯수세기
$result1= mysql_query($query_count, $connect);
$temp= mysql_fetch_array($result1);
$totals= $temp[0];

$new=$totals+1;
$sql="ALTER TABLE contract AUTO_INCREMENT=$new" ;  //$new 따옴표서도 안되고 식을 써도 안되고..홀로써야
mysql_query($sql,$connect);


if(!$sql)die("테이블 관리에 실패 하였습니다.".mysql_error());
if($sql)echo ("정상적으로 실행 되었습니다.");


?>


<!--

//한줄만 지울때 : 테이블 퍼슨에서 래스트내임 항목이  그리픈인것만 지울때
mysql_query("DELETE FROM Persons WHERE LastName='Griffin'");
mysql_close($con);

//테이블은 남기고 테이블 안의 줄만 전체 삭제 한후.. 다시 오토인크리먼트 초기화
$sql="TRUNCATE TABLE payment" ;
mysql_query($sql,$connect);

// 기존 데이터가 있을 경우 초기값을  1부터 부여하고 , 맨끝에 다시 그 다음 숫자로 초기하한다.
//아무것도 없는 테이블은 그냥 다시 1부터 다시 초기하됨

$sql="ALTER TABLE payment AUTO_INCREMENT=1" ;
mysql_query($sql,$connect);

$sql="SET @COUNT = 0 ";
mysql_query($sql,$connect);

$sql="UPDATE `payment` SET `payment`.`no` = @COUNT:=@COUNT+1 ";
mysql_query($sql,$connect);

$sql="ALTER TABLE payment AUTO_INCREMENT=14" ;
mysql_query($sql,$connect);

if(!$sql)die("테이블 관리에 실패 하였습니다.".mysql_error());
if($sql)echo ("정상적으로 실행 되었습니다.");



//수당테이블의 번호를 보기좋게 정리해준다.***************************

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
$sql="ALTER TABLE payment AUTO_INCREMENT=$new" ;  //$new 따옴표서도 안되고 식을 써도 안되고..홀로써야
mysql_query($sql,$connect);









//1. 테이블 생성
$sql="CREATE TABLE member
     (no int not null auto_increment,
	 PRIMARY KEY(no),
	 id char(15),
	 user_id char(15),
	 name char(15),
	 nick_name char(15),
	 birth char(8),
	 sex char(6),
	 tel char(8),
     email char(40),
	 pw char(32),
	 addr_1 varchar(100),
	 addr_2 varchar(100),
	 level int,
	 regdate char(20),
	 ip char(20)
      )";

if(!$sql)die("테이블 생성에 실패 하였습니다.".mysql_error());

if($sql)echo ("정상적으로 실행 되었습니다.");
mysql_query($sql,$connect);


//2. 테이블 삭제
$sql="drop table member";  // 'member'라는 테이블 삭제하기

//3. 컬럼 삭제
//member 라는 테이블 안에 aas라는컬럼 삭제하기
$sql="alter table member drop column aas";

//4/ [rename]테이블 이름변경
//member라는 테이블명을 member_2로 변경
$sql="alter table member rename member_2";

//5.필드명 추가
//alter table , add 명령어를 이용하여 필드와 타입을 추가 해보자
$sql="alter table member add age int";

//6.필드명 및 타입 변경
//[change]필드명 변경(age 필드명을 age_2바꾸고 타입을 varchar 형식으로)
$sql="alter table member change age age_2 varchar(20)";
varchar 형식은 길이값을 괄호안에 넣어 줘야 한다는거....

//7 /[modify] 필드 타입만 수정하기
//(age필드가 int형식으로 바뀜)
$sql="alter table member modify age int ";

//8//존재하고 있는 테이블 보기!
$sql = "SHOW TABLES FROM 테이블이름 ";
$result = mysql_query($sql);
if (!$result) {
    echo 'MySQL Error: ' . mysql_error();
    exit;
}
while ($row = mysql_fetch_row($result)) {
    echo "Table: {$row[0]}<br>";
}
 -->
