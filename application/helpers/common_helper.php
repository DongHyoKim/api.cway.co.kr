<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * $t1 기준시간(없으면 현재시간으로 대체) 
 * $t2 비교시간
 * PHP 5.3 이상. 
 */ 
ini_set('display_errors', 1); // php 에러 표시하기

function passwdEncrypt($pw){
	// md5 + sha1 128 x5
	return sha1(md5(sha1(md5(sha1(md5(sha1(md5(sha1(md5($pw))))))))));
}

function DateDiff($dt1, $dt2) {
	/** 사용법
	echo DateDiff('2010-07-27 12:11:13', '2319-09-11 21:52:05'); // 309년 후
	echo DateDiff('2010-07-27 12:11:13', '2010-07-21 15:38:42'); // 5일 전
	echo DateDiff(null, '2010-06-11 15:38:42'); // (현재시간 기준) 1개월 전
	echo DateDiff(null, '2010-07-27 08:38:42'); // (현재시간 기준) 25분 전
	*/	 
	if(!$dt2) return; 
	//$trans = array('y' => '년', 'm' => '개월', 'd' => '일', 'h' => '시간', 'i' => '분', 's' => '초');
	//$ago = array(' 후', ' 전'); 
	$dt1 = new DateTime($dt1); 
	$dt2 = new DateTime($dt2); 
	$t1 = $dt1->diff($dt2);

	// $to_time = strtotime($dt1);
	// $from_time = strtotime($dt2); 
	// $minutes = round(abs($to_time - $from_time) / 60, 2);

	if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 1)
		return "방금 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 60 )
		return $t1->i . "분 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h >= 1)
		return "{$t1->h}시간 전";
	else if($t1->y == 0 && ( $dt1->format('Y') == $dt2->format('Y') ) )
		return $dt2->format('m.d A h:i');
	else
		return $dt2->format('Y.m.d');
} 

// PRINT STREAM
function stripString($string){
	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
	$string = nl2br($string);
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

function stripString_HTML($string){
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

// AUTOLINK STREAM
function autoLink($text){
	//$pattern = "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))?(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$text = preg_replace($pattern, " <a href='$1' target='_blank' class='link'>$1</a>", $text);
	$text = preg_replace("/href='www/", "href='http://www", $text);
	return $text;
}
