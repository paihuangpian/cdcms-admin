<?php 

namespace Admin\Controller;
use Think\Controller;
class CommonController extends AuthController{

	protected $Post = null;
	protected $Get = null;

	public function _initialize(){
		if( !IS_AJAX ) exit( $this->show('Could not locate remote server.') );
		if( !session('?user') ) {
			$url = '<script>window.location.reload()</script>';
			exit( $this->ajaxReturn( ['arr'=>$url,'status'=>1,] ) );
		}

		$post = array_filter( I('post.') );
		$get = array_filter( I('get.') );
		//@$post['password'] = md5(@$post['password']);
		$this->Post = $post;
		$this->Get = $get;
	}

	public function _empty($name){
		exit('Could not locate remote server.');
	}

	public function _before_index(){

	}

	public function fnAjax( $arr, $status=1, $url=null ){
		return $this->ajaxReturn( array( 
			'arr' 		=> $arr,
			'status' 	=> $status,
			'url' 		=> $url,
		) );
	}

	public function fnPage( $val, $tep, $num=2, $field=null, $the=false ){
		$m = D($val);
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->order('id desc')->field( $field, $the )->limit( $page->firstRow.','.$page->listRow, $num )->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		return array( $this->fetch(T($tep)), 1 );
	}

	public function fnDetail( $id, $val, $field=null, $the=false ){
		$m = D($val);
		$row = $m->where( 'id='.$id )->field( $field, $the )->find();
		if( !$row ) return array( $m->getError(), 0 );
		return array( $row, 1 );
	}

	public function fnAdd( $val, $data, $field=null, $the=false ){
		$m = D($val);
		$oD = $m->_auto( $m, $data );
		if( !$oD ) return array( $m->msg, 0 );
		$res = $m->add($oD);
		return array( '添加成功！', 1 );
	}

	public function fnEdit( $val, $data, $field=null, $the=false ){
		$m = D($val);
		$oD = $m->_auto( $m, $data );
		if( !$oD ) return array( $m->msg, 0 );
		$row = $m->where( 'id='.$data['id'] )->field( $field, $the )->save($oD);
		if( $row ) return array( '编辑成功！', 1 );
		return array( $m->getError(), 0 );
	}

	public function fnDel( $id, $val ){
		$m = D($val);
		$res = $m->where( 'id='.$id )->delete();
		if( $res ) return array( '删除成功！', 1 );
		return array( $m->getError(), 0 );
	}

}

class class_ftp
{
    public $off; // 返回操作状态(成功/失败)
    public $conn_id; // FTP连接
    /**
     * 方法：FTP连接
     * @FTP_HOST -- FTP主机
     * @FTP_PORT -- 端口
     * @FTP_USER -- 用户名
     * @FTP_PASS -- 密码
     */
    function __construct($FTP_HOST,$FTP_PORT,$FTP_USER,$FTP_PASS)
    {
        $this->conn_id = @ftp_connect($FTP_HOST,$FTP_PORT) or die("FTP服务器连接失败");
        @ftp_login($this->conn_id,$FTP_USER,$FTP_PASS) or die("FTP服务器登陆失败");
        @ftp_pasv($this->conn_id,1); // 打开被动模拟
    }
    /**
     * 方法：上传文件
     * @path -- 本地路径
     * @newpath -- 上传路径
     * @type -- 若目标目录不存在则新建
     */
    function up_file($path,$newpath,$type=true)
    {
        if($type) $this->dir_mkdirs($newpath);
        $this->off = @ftp_put($this->conn_id,$newpath,$path,FTP_BINARY);
        if(!$this->off) echo "文件上传失败，请检查权限及路径是否正确！";
    }
    /**
     * 方法：移动文件
     * @path -- 原路径
     * @newpath -- 新路径
     * @type -- 若目标目录不存在则新建
     */
    function move_file($path,$newpath,$type=true)
    {
        if($type) $this->dir_mkdirs($newpath);
        $this->off = @ftp_rename($this->conn_id,$path,$newpath);
        if(!$this->off) echo "文件移动失败，请检查权限及原路径是否正确！";
    }
    /**
     * 方法：复制文件
     * 说明：由于FTP无复制命令,本方法变通操作为：下载后再上传到新的路径
     * @path -- 原路径
     * @newpath -- 新路径
     * @type -- 若目标目录不存在则新建
     */
    function copy_file($path,$newpath,$type=true)
    {
        $downpath = "c:/tmp.dat";
        $this->off = @ftp_get($this->conn_id,$downpath,$path,FTP_BINARY);// 下载
        if(!$this->off) echo "文件复制失败，请检查权限及原路径是否正确！";
        $this->up_file($downpath,$newpath,$type);
    }
    /**
     * 方法：删除文件
     * @path -- 路径
     */
    function del_file($path)
    {
        $this->off = @ftp_delete($this->conn_id,$path);
        if(!$this->off) echo "文件删除失败，请检查权限及路径是否正确！";
    }
    /**
     * 方法：生成目录
     * @path -- 路径
     */
    function dir_mkdirs($path)
    {
        $path_arr = explode('/',$path); // 取目录数组
        $file_name = array_pop($path_arr); // 弹出文件名
        $path_div = count($path_arr); // 取层数
        foreach($path_arr as $val) // 创建目录
        {
            if(@ftp_chdir($this->conn_id,$val) == FALSE)
            {
                $tmp = @ftp_mkdir($this->conn_id,$val);
                if($tmp == FALSE)
                {
                    echo "目录创建失败，请检查权限及路径是否正确！";
                    exit;
                }
                @ftp_chdir($this->conn_id,$val);
            }
        }
        for($i=1;$i=$path_div;$i++) // 回退到根
        {
            @ftp_cdup($this->conn_id);
        }
    }
    /**
     * 方法：关闭FTP连接
     */
    function close()
    {
        @ftp_close($this->conn_id);
    }
}// class class_ftp end
/************************************** 测试 ***********************************
$ftp = new class_ftp('192.168.100.143',21,'user','pwd'); // 打开FTP连接
//$ftp->up_file('aa.txt','a/b/c/cc.txt'); // 上传文件
//$ftp->move_file('a/b/c/cc.txt','a/cc.txt'); // 移动文件
//$ftp->copy_file('a/cc.txt','a/b/dd.txt'); // 复制文件
//$ftp->del_file('a/b/dd.txt'); // 删除文件
$ftp->close(); // 关闭FTP连接
******************************************************************************/


?>