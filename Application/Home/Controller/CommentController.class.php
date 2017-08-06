<?php
	namespace Home\Controller;

		class CommentController extends BaseController{
			/*动态获取后台评论数据*/
			public function Comment4Admin(){
				$db = M('consultComment');

				$where['consult_id'] = $_POST['consultId'];
				$where['type'] = 1;

				$data = $db->order('comment_id DESC')->where($where)->select();
				if($data){
					$data = self::unlimited($data,'reply','parent_id','comment_id');
				}else{
					exit('NULL');
				}

				echo json_encode($data);
			}

			public function Comment4User(){
				$db = M('consultComment as a');

				$where['consult_id'] = $_POST['consultId'];
				$where['type'] = 0;

				$data = $db
						->field('a.*,b.head_pic')
						->join("tp_users as b on a.user_id=b.user_id")
						->order('comment_id DESC')
						->where($where)
						->select();
				if($data){
					$data = self::unlimited($data,'reply','parent_id','comment_id');
				}else{
					exit('NULL');
				}
				echo json_encode($data);
			}

			public function Handle(){
				$model['comment_id'] = uniqid();
				$model['consult_id'] = $_POST['consultId'];
				$model['createtime'] = time();
				$model['comment'] = $_POST['comment'];
				$model['parent_id'] = $_POST['parentId'];
				$model['parent_name'] = $_POST['parentName'];
				$model['user_id'] = session('user.user_id');
				$model['nickname'] = session('user.nickname');
				$model['type'] = $_POST['type'];
				$model['ip_address'] = get_client_ip();
				$db = M('consultComment');
				$db->add($model);

				redirect(U('Consult/detail/?id='.$model['consult_id']));
			}

			Static public function unlimited($cate,$name='FORWARD',$pid='PID',$mid='MID',$pidSign,$sumSign){
				$result = array();
				foreach ($cate as $k => $v) {	
					if($v[$pid] == $pidSign){
						unset($v[$k]);
						$v[$name] = self::unlimited($cate,$name,$pid,$mid,$v[$mid],$sumSign);
						if(!empty($sumSign) && isset($v[$name])){
							$v[$sumSign] = count($v[$name]);
							foreach ($v[$name] as $value){
								$v[$sumSign] += $value[$sumSign];
							}
						}
						$result[]=$v;
					}
				}
				return $result;
			}
		}
	?>