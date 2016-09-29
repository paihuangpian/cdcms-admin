<?php 

namespace Admin\Controller;
use Think\Controller;
class OrdersController extends CommonController{

	public function index(){
		$get = $this->Get;
		$m = D('Orders as a');
		$num = 2;
		$count = $m->count('id');
		$page = new \Think\Page( $count, $num );
		$show = $page->show();
		$list =	$m->join('users as b on b.id = a.user_id')
				  ->join('carts as c on c.user_id = a.user_id')
				  ->field('b.name,a.*,c.*')
				  ->order('a.id desc')
				  ->limit( $page->firstRow.','.$page->listRow, $num )
				  ->select();
		$this->assign( 'list', $list );
		$this->assign( 'show', $show );
		$content = $this->fetch(T('Orders/index'));
		//if( $list ) return $this->ajaxReturn( ajaxData( $content, 1 ) );
		return $this->fnAjax( $content );
	}

	public function detail(){
		$get = $this->Get;
		$_db = D('Orders');
		$where = array( 'id'=>$get['id'] );
		$order = $_db->where($where)->find();
		$_db = D('Users');
		$where = array( 'id'=>$order['user_id'] );
		$user = $_db->where($where)->find();
		$_db = D('Carts');
		$where = array( 'user_id'=>$order['user_id'] );
		$carts = $_db->where($where)->select();
		$_db = D('Classes');
		foreach ($carts as $key => $value) {
			$where = array( 'id'=>$value['class_id'] );
			$classes = $_db->where($where)->find();
			foreach ($value as $k => $v) @$goods['c_'.$k] = $v;
			foreach ($classes as $k => $v) @$goods['s_'.$k] = $v;
			$carts[$key] = $goods;
		}
		
		$this->assign( 'order', $order );
		$this->assign( 'user', $user );
		$this->assign( 'carts', $carts );
		$content = $this->fetch( T('Orders/edit') );
		return $this->fnAjax( $content );
		//return $this->ajaxReturn( ajaxData( null, 0 ) );
	}

	public function edit(){
		// $post = $this->Post;
		// $m = D('Orders');
		// $where = array(
		// 	'id' => $post['id'],
		// );
		// $row = $m->where( $where )->save($post);
		// if( $row ) {
		// 	return $this->fnAjax( '编辑成功！', 1, U('index') );
		// }
		// return $this->fnAjax( '编辑失败！', 0 );
		$post = $this->Post;
		$_db = D('Orders');
		$where = array( 'id'=>$post['id'] );
		$res = $_db->field('status')->where($where)->save($post);
		return $this->fnAjax( '编辑成功！' );
	}

	public function add(){
		$post = $this->Post;
		$res = $this->fnAdd( 'Orders', $post );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

	public function del(){
		$get = $this->Get;
		$res = $this->fnDel( $get['id'], 'Orders' );
		return $this->fnAjax( $res[0], $res[1], U('index') );
	}

}


?>