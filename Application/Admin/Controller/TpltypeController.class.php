<?php 

namespace Admin\Controller;
use Think\Controller;
/**
* 
*/
class TpltypeController extends CommonController
{
	protected $tableName = 'Tpltype';
	public function index()
	{
		$post = $this->Post;
		$m = M('template_type');
		$num = 10;
		$count = $m->where('pid=0')->count('pid');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->order('id desc')
				  ->limit( $page->firstRow.','.$page->listRow, $num )
				  ->where('pid=0')
				  ->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch( T('Tpltype/index') );
		return $this->fnAjax( $content );
	}

	public function detail()
	{
		$get = $this->Get;
		$_db = D('Tpltype');
		$where = array( 'id'=>$get['id'] );
		$tpl = $_db->where($where)->find();
		$list = $_db->select();
		$this->assign( 'type', $tpl );
		$this->assign( 'list', $list );
		$content = $this->fetch( T('Tpltype/edit') );
		return $this->fnAjax($content);
	}

	public function edit()
	{
		$post = $this->Post;
		$m = D($this->tableName);
		$where = array( 'name'=>$post['name'] );
		$row = $m->where($where)->find();
		if( $row ) return $this->fnAjax( '已存在同名', 0 );
		$where = array( 'id'=>$post['id'] );
		$row = $m->where($where)->field( 'id', true )->save($post);
		return $this->fnAjax( '编辑成功！', 1, U('index') );
	}

	public function add()
	{
		if( IS_GET ){
			$_db = D('Template_type');
			$type = $_db->select();
			$this->assign( 'type', $type );
			$content = $this->fetch( T('tpltype/add') );
			return $this->fnAjax($content);
		}
		$post = $this->Post;
		$res = $this->fnAdd( $this->tableName, $post );
		return $this->fnAjax( $res[0], $res[1], U('index') );
		
	}

	public function del()
	{
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], $this->tableName );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function Son()
	{
		$get = $this->Get;
		$_db = M('template_type');
		$where = array('pid'=>$get['id']);
		$list = $_db->where($where)->select();
		if( empty($list) ) return false;
		$this->assign('list', $list);
		$content = $this->fetch('/Tpltype/a');
		return $this->fnAjax($content);
	}

}

?>