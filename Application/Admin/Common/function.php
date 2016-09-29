<?php 
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
function msg( $msg, $state, $url=null ){
	return [ 'msg'=>$msg, 'state'=>$state, 'url'=>$url ];
}
function push(){
	$host = parse_url($_SERVER['HTTP_REFERER']);
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $k == $host['host'] ){
			return true;
		}
	}
	return false;
}

function ajaxData( $arr, $state, $url=null ){
	return [ 'arr'=>$arr, 'status'=>$state, 'url'=>$url ];
}


function Referer(){
	$host = parse_url($_SERVER['HTTP_REFERER']);
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $v == $host['host'] ){
			return true;
		}
	}
	return false;
}

function isServerName(){
	$name = $_SERVER["SERVER_NAME"];
	$url = C('HOST_URL');
	foreach( $url as $k=>$v ){
		if( $v == $name ){
			return true;
		}
	}
	return false;
}

function unzip_file( $file, $destination )
{
	$zip = new ZipArchive();
	if( $zip->open($file) !== true ){
		return '失败';
	}
	$zip->extractTo($destination);
	$zip->close();
}

function ezip($zip, $hedef = ''){
    $dirname=preg_replace('/.zip/', '', $zip);
    $root = $_SERVER['DOCUMENT_ROOT'].'/zip/';
    // echo $root. $zip;
    $zip = zip_open($root . $zip);
    // var_dump($zip);
    @mkdir($root . $hedef . $dirname.'/'.$zip_dosya);
    while($zip_icerik = zip_read($zip)){
        $zip_dosya = zip_entry_name($zip_icerik);
        if(strpos($zip_dosya, '.')){
            $hedef_yol = $root . $hedef . $dirname.'/'.$zip_dosya;
            @touch($hedef_yol);
            // echo $hedef_yol;
            $yeni_dosya = @fopen($hedef_yol, 'w+');
            @fwrite($yeni_dosya, zip_entry_read($zip_icerik));
            @fclose($yeni_dosya); 
            // $yeni_dosya;
        }else{
            @mkdir($root . $hedef . $dirname.'/'.$zip_dosya);
            // echo $root . $hedef . 'x/'.$zip_dosya;
        };
    };
}

?>