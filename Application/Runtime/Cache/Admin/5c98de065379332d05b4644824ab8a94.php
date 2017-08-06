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
				          <form class="navbar-form form-inline" action="<?php echo U('Activity/activity_list');?>" method="post">
				            <div class="form-group">
				              	<input type="text" name="keywords" class="form-control" placeholder="请输入活动标题">
				            </div>
				           	
				            <button type="submit" class="btn btn-default">提交</button>
				            <div class="form-group pull-right">
					            <a href="<?php echo U('Activity/activity');?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新课程</a>
				            </div>		          
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
		                   <tr role="row" >
			                   <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 154px;">课程标题</th>
			                   <!-- <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Browser: activate to sort column ascending">活动内容</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">活动描述</th> -->
			                   <th class="sorting" style="width: 150px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">课程内容</th>
			                   <th class="sorting" style="width: 150px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">课程讲师</th>
			                   <th class="sorting" style="width: 120px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">报名截止时间</th>
			                   <th class="sorting" style="width: 150px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">课程地址</th>
			                   <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">开始时间</th>
			                   <!-- <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">结束时间</th> -->
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Platform(s): activate to sort column ascending">显示</th>
			                   <th class="sorting" style="width: 60px;" tabindex="0" aria-controls="example1"  aria-label="Engine version: activate to sort column ascending">发布时间</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Engine version: activate to sort column ascending">排序</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="Engine version: activate to sort column ascending">浏览人数</th>
			                   <th class="sorting" tabindex="0" aria-controls="example1"  aria-label="CSS grade: activate to sort column ascending">操作</th>
		                   </tr>
		                 </thead>
						<tbody>
						  <?php if(is_array($list)): foreach($list as $k=>$vo): ?><tr role="row" align="center">
		                     <td><?php echo (getSubstr($vo["title"],0,8)); ?></td>
		                     <td><?php echo (getSubstr($vo["content"],0,8)); ?></td>
		                     <td><?php echo (getSubstr($vo["teacher"],0,8)); ?></td>
		                     <td><?php echo ($vo["activity_time"]); ?></td>
		                     <td><?php echo (getSubstr($vo["activity_address"],0,8)); ?></td>
		                     <td><?php echo ($vo["start_time"]); ?></td>
		                     <!-- <td><?php echo ($vo["end_time"]); ?></td> -->
		                     <td>
                                 <img width="20" height="20" src="/Public/images/<?php if($vo[is_show] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('activity','activity_id','<?php echo ($vo["activity_id"]); ?>','is_show',this)"/>                                        
                            </td>
	                        <td><?php echo ($vo["add_time"]); ?></td>
							<td class="text-center">                         
		                        <input type="text" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onchange="updateActivitySort('activity_id','<?php echo ($vo["activity_id"]); ?>','sort',this)" size="4" value="<?php echo ($vo["sort"]); ?>" />
		                    </td>
							<td><?php echo ($vo["click"]); ?></td>
		                     <td>
		                      <!-- <a target="_blank" href="<?php echo U('Home/Article/detail',array('article_id'=>$vo['article_id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye"></i></a> -->
		                      <a class="btn btn-primary" href="<?php echo U('Activity/activity',array('activity_id'=>$vo['activity_id']));?>"><i class="fa fa-pencil"></i></a>
		                      <a class="btn btn-danger" href="javascript:void(0)" data-url="<?php echo U('Activity/activityDel');?>" data-id="<?php echo ($vo["activity_id"]); ?>" onclick="delfun(this)"><i class="fa fa-trash-o"></i></a>
                              <a href="javascript:void(0);" onclick="ClearAritcleHtml('<?php echo ($vo[article_id]); ?>')" class="btn btn-default" title="清除静态缓存页面"><i class="fa fa-fw fa-refresh"></i></a>
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
// 修改指定表的排序字段
function updateActivitySort(id_name,id_value,field,obj)
{		     
 		var value = $(obj).val();
		$.ajax({
				url:"<?php echo U('Activity/changeTableSort');?>?id_name="+id_name+"&id_value="+id_value+"&field="+field+'&value='+value,			
				success: function(data){
					if( data.code == 200){
						layer.msg('更新成功', {icon: 1});
					}else{
						$(obj).val(data);
						layer.msg('更新失败', {icon: 2});
					}								   
				}
		});		
}

function delfun(obj){
	if(confirm('确认删除')){		
		$.ajax({
			type : 'post',
			url : $(obj).attr('data-url'),
			data : {act:'del',activity_id:$(obj).attr('data-id')},
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
 
    /*
     * 清除文章静态页面缓存
     */
    function ClearAritcleHtml(article_id)
    {
    	$.ajax({
                    type:'GET',
                    url:"<?php echo U('Admin/System/ClearAritcleHtml');?>",
                    data:{article_id:article_id},
                    dataType:'json',
                    success:function(data){
                            layer.alert(data.msg, {icon: 2});								 
                    }
		});
    }
    
</script>
</body>
</html>