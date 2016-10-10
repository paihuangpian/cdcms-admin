<?php 

namespace Admin\Controller;
use Think\Controller;
/**
* 
*/
class TemplateController extends CommonController
{
	protected $tableName = 'Templates';
	public function index()
	{
		$m = D('Templates');
		$count = $m->count('id');
		$num = 10;
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->order('id desc')->limit( $page->firstRow.','.$page->listRow, $num )->select();
		$m = D('Tpltype');
		foreach ($list as $key => $value) {
			$where = array( 'id'=>$value['type'] );
			$type = $m->where($where)->find();
			$list[$key]['type'] = $type['name'];
		}
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch( T('Templates/index') );
		return $this->fnAjax( $content, 1 );
	}

	public function detail()
	{
		$get = $this->Get;
		$_db = D('Templates');
		$where = array( 'id'=>$get['id'] );
		$tpl = $_db->where($where)->find();
		$_db = D('Template_type');
		$where = array( 'id'=>$tpl['type'] );
		$type = $_db->where($where)->field('id',true)->find();
		foreach ($tpl as $key => $value) @$row['m_'.$key] = $value;
		foreach ($type as $key => $value) @$row['t_'.$key] = $value;
		$type = $_db->select();
		$this->assign( 'row', $row );
		$this->assign( 'type', $type );
		$content = $this->fetch( T('Templates/edit') );
		return $this->fnAjax($content);
	}

	public function edit()
	{
		$post = $this->Post;
		$post['path'] = $post['pDir'].$post['dir'].'/';
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
			$content = $this->fetch( T('Templates/add') );
			return $this->fnAjax($content);
		}
		$post = $this->Post;
		$post['path'] = $post['pDir'].$post['dir'].'/';
		$res = $this->fnAdd( $this->tableName, $post );
		return $this->fnAjax( $res[0], $res[1], U('index') );
		
	}

	public function del()
	{
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], $this->tableName );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function upload(){
		$upload = new \Think\Upload();
		$upload->maxSize = 3145728;
		$upload->exts = [ 'jpg', 'gif', 'png', 'jpeg' ];
		$upload->rootPath = './Uploads/Admin/';
		$upload->savePath = '';
		$upload->saveRule = ' ';
		$upload->replace = true;
		$upload->hash = false;
		$upload->subName = date('Y-m-d',time()).'/';
		$info = $upload->upload();
		if( !$info ){
			return $this->fnAjax( ['msg'=>$upload->getError()], 0 );
		}
		foreach($info as $file){
            $FilePath = $file['savename'];
        }
        $url = $upload->rootPath.$upload->subName.$FilePath;
		$msg = [ 'msg'=>'上传成功', 'url'=>'/Uploads/Admin/'.$upload->subName.$FilePath, ];
		unzip_file($url,'./Templates/');
		return $this->fnAjax( $msg );
	}

}

?>