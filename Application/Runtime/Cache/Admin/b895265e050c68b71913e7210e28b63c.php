<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>果本管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
 	<link href="/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <link href="/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />   
    <!-- jQuery 2.1.4 -->
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="/Public/js/global.js"></script>
    <script src="/Public/js/myFormValidate.js"></script>    
    <script src="/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/Public/js/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/Public/js/myAjax.js"></script>
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    //全选
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }   
    
    function get_help(obj){
        layer.open({
            type: 2,
            title: '帮助手册',
            shadeClose: true,
            shade: 0.3,
            area: ['90%', '90%'],
            content: $(obj).attr('data-url'), 
        });
    }
    
    function delAll(obj,name){
    	var a = [];
    	$('input[name*='+name+']').each(function(i,o){
    		if($(o).is(':checked')){
    			a.push($(o).val());
    		}
    	})
    	if(a.length == 0){
    		layer.alert('请选择删除项', {icon: 2});
    		return;
    	}
    	layer.confirm('确认删除？', {btn: ['确定','取消'] }, function(){
    			$.ajax({
    				type : 'get',
    				url : $(obj).attr('data-url'),
    				data : {act:'del',del_id:a},
    				dataType : 'json',
    				success : function(data){
    					if(data == 1){
    						layer.msg('操作成功', {icon: 1});
    						$('input[name*='+name+']').each(function(i,o){
    							if($(o).is(':checked')){
    								$(o).parent().parent().remove();
    							}
    						})
    					}else{
    						layer.msg(data, {icon: 2,time: 2000});
    					}
    					layer.closeAll();
    				}
    			})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);	
    }
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

	<section class="content">
       <div class="row">
       		<div class="col-xs-12">
	       		<div class="box">
	           	<div class="box-header">
	                <nav class="navbar navbar-default">	     
				        <div class="collapse navbar-collapse">
				          <form class="navbar-form form-inline" action="<?php echo U('Activity/activity_applicant_list');?>" method="post">
				            <div class="form-group">
				              	<input type="text" name="keywords" class="form-control" placeholder="请输入姓名或手机号" value="<?php echo ($keywords); ?>">
				            </div>
				           	
				            <button type="submit" class="btn btn-default">提交</button>
				            <!-- <div class="form-group pull-right">
					            <a href="<?php echo U('Activity/activity');?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新活动</a>
				            </div> -->		          
				          </form>		
				      	</div>
	    			</nav>               
	             </div>	    
	             <!-- /.box-header -->
	             <div class="box-body">	             
	           		<div class="row">
	            	<div class="col-sm-12">
		              <table id="list-table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
		                 <thead>
		                   <tr role="row">
		                   		<th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 154px;">申请ID</th>
			                   <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 154px;">活动标题</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Browser: activate to sort column ascending">姓名</th>
			                   <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">手机号</th>
			                   <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">性别</th>
			                   <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">年龄</th>
			                   <th class="sorting" style="width: 80px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">申请时间</th>
			                   <th class="sorting" style="width: 100px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">备注</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Engine version: activate to sort column ascending">申请状态</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Engine version: activate to sort column ascending">审核时间</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="CSS grade: activate to sort column ascending">操作</th>
		                   </tr>
		                 </thead>
						<tbody>
						  <?php if(is_array($list)): foreach($list as $k=>$vo): ?><tr role="row" align="center" >
						  		<td><?php echo ($vo["entry_id"]); ?></td>
		                     	<td><?php echo (getSubstr($vo["title"],0,33)); ?></td>
		                     	<td><?php echo ($vo["name"]); ?></td>
		                     	<td><?php echo ($vo["phone"]); ?></td>
		                     	<td><?php echo ($vo["sex"]); ?></td>
		                     	<td><?php echo ($vo["age"]); ?></td>
		                     	<td><?php echo ($vo["add_time"]); ?></td>
		                     	<td><?php echo (getSubstr($vo["mark"],0,33)); ?></td>
		                     	<!-- <td><?php echo (getSubstr($vo["suggest"],0,33)); ?></td> -->
		                     	<td style="color:red" class="status_<?php echo ($vo["entry_id"]); ?>"><?php echo ($vo["is_status"]); ?></td>
		                     	<td class="time_<?php echo ($vo["entry_id"]); ?>"><?php echo ($vo["check_time"]); ?></td>
		                     	<td>
		                      		<!-- <a target="_blank" href="<?php echo U('Home/Article/detail',array('article_id'=>$vo['article_id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye"></i></a> -->
		                      		<!-- <a class="btn btn-primary" href="<?php echo U('Activity/activity',array('activity_id'=>$vo['activity_id']));?>"><i class="fa fa-pencil"></i></a> -->
									<a href="javascript:void(0)"  data-id="1" class="btn btn-info" onclick="check(this,<?php echo ($vo["entry_id"]); ?>,<?php echo ($vo["activity_id"]); ?>)">通过</a>
	                      			<a href="javascript:void(0)"  data-id="2" class="btn btn-primary"  onclick="check(this,<?php echo ($vo["entry_id"]); ?>,<?php echo ($vo["activity_id"]); ?>)">拒绝</a>

		                      		<a class="btn btn-danger" href="javascript:void(0)" data-url="<?php echo U('Activity/checkDle');?>" data-id="<?php echo ($vo["entry_id"]); ?>" onclick="delfun(this)"><i class="fa fa-trash-o"></i></a>
				     			</td>
		                    </tr><?php endforeach; endif; ?>
		                   </tbody>
		                 <tfoot>
		                 
		                 </tfoot>
		               </table>
	               </div>
	          </div>
              <div class="row">
              	    <div class="col-sm-6 text-left"></div>
                    <div class="col-sm-6 text-right"><?php echo ($page); ?></div>		
              </div>
	          </div><!-- /.box-body -->
	        </div><!-- /.box -->
       	</div>
       </div>
   </section>
</div>
<script>
//审核
function check(obj,entry_id,activity_id){
	var is_status = $(obj).attr('data-id');
	var confirmMsg = is_status == 1 ? '审核通过' : '拒绝';
	if(confirm('确认'+confirmMsg+'该用户申请？')){
		$.ajax({
			type : 'post',
			url : "<?php echo U('Activity/checkApplicant');?>",
			data : {is_status:is_status,entry_id:entry_id,activity_id:activity_id},
			dataType : 'json',
			success : function(data){
				if(data.code == 200){
					$('.time_'+entry_id).html(data.check_time);
					$('.status_'+entry_id).html(data.is_status);
					layer.alert('操作成功！', {icon: 1});
				}else if( data.code ==400 ){
					layer.alert('操作失败，稍候再试！', {icon: 2});  
				}else{
					layer.alert('非法操作，请联系技术人员！', {icon: 2});
				}
			}
		})
	}
	return false;
}
function delfun(obj){
	if(confirm('确认删除')){		
		$.ajax({
			type : 'post',
			url : $(obj).attr('data-url'),
			data : {act:'del',entry_id:$(obj).attr('data-id')},
			dataType : 'json',
			success : function(data){
				if(data){
					$(obj).parent().parent().remove();
				}else{
					layer.alert('删除失败', {icon: 2});  //alert('删除失败');
				}
			}
		})
	}
	return false;
}
 
</script>
</body>
</html>