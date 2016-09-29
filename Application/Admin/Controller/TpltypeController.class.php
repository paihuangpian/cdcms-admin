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
		$get = $this->Get;
		$content = $this->fnPage( $this->tableName, 'Tpltype/index', 5 );
		return $this->fnAjax( $content[0], $content[1] );
	}

	public function detail()
	{
		$get = $this->Get;
		$_db = D('Tpltype');
		$where = array( 'id'=>$get['id'] );
		$tpl = $_db->where($where)->find();
		$this->assign( 'type', $tpl );
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
			$content = $this->fetch( T('Tpltype/add') );
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

}

?>