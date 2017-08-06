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
 

<script type="text/javascript">
    window.UEDITOR_Admin_URL = "/Public/plugins/Ueditor/";
    var URL_upload = "<?php echo ($URL_upload); ?>";
    var URL_fileUp = "<?php echo ($URL_fileUp); ?>";
    var URL_scrawlUp = "<?php echo ($URL_scrawlUp); ?>";
    var URL_getRemoteImage = "<?php echo ($URL_getRemoteImage); ?>";
    var URL_imageManager = "<?php echo ($URL_imageManager); ?>";
    var URL_imageUp = "<?php echo ($URL_imageUp); ?>";
    var URL_getMovie = "<?php echo ($URL_getMovie); ?>";
    var URL_home = "<?php echo ($URL_home); ?>";    
</script>

<script type="text/javascript" src="/Public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/Public/plugins/Ueditor/ueditor.all.js"></script>
<link href="/Public/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="/Public/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="/Public/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>

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
			<div class="col-md-12">
			
			<div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">发布课程</h3>
                  <a href="<?php echo U('Activity/activity_list');?>" data-toggle="tooltip" title="" class="btn btn-default pull-right" data-original-title="返回"><i class="fa fa-reply"></i></a>
                </div>
                <form class="form-horizontal" action="<?php echo U('Activity/activity');?>" id="add_post" method="post">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">课程标题</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["title"]); ?>" name="title" >
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">课程讲师</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["teacher"]); ?>" name="teacher" >
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">主要课程</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["course"]); ?>" name="course" >
                      </div>
                    </div>
                    <div class="form-group">
                    	<label for="text" class="col-sm-2 control-label">课程宣传图</label>                   	
                    	<div class="col-sm-8">
                        <input type="text" id="imagetext" name="activity_logo" value="<?php echo ($info["activity_logo"]); ?>">
                        <input type="button" class="button" onClick="GetUploadify(1,'imagetext','article','')"  value="上传图片"/>
                        <span style="color:red">*请上传400*260以上图</span>
                      </div>
                    </div>
                    <!-- <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">seo关键字</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" name="keywords"  value="<?php echo ($info["keywords"]); ?>">
                      </div>
                    </div> -->
                    <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">报名截止时间</label>
                      <div class="col-sm-8">
                            <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                            </i>
                                    </span>
                                <input type="text" class="form-control" id ="activity_time" name="activity_time"  value="<?php echo ($info["activity_time"]); ?>">                                            
                            </div>                        
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">上课地址</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["activity_address"]); ?>" name="activity_address" >
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">课程人数</label>
                      <div class="col-sm-8">
                        <?php if($info[people_num] > 0): ?><input type="text" value="<?php echo ($info["people_num"]); ?>" class="form-control" style="width:150px;" id="people_num" name="people_num" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                        <?php else: ?>
                            <input type="text" value="20" class="form-control" style="width:150px;" id="people_num" name="people_num" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" /><?php endif; ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">开课时间</label>
                      <div class="col-sm-8">
                            <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                            </i>
                                    </span>
                                <input type="text" class="form-control" id="start_time" name="start_time"  value="<?php echo ($info["start_time"]); ?>">                                            
                            </div>                        
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">上课时间</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["course_time"]); ?>" name="course_time" >
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">午休时间</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo ($info["lunch_time"]); ?>" name="lunch_time" >
                      </div>
                    </div>
                   <!--  <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">结束时间</label>
                      <div class="col-sm-8">
                            <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon">
                                            <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                            </i>
                                    </span>
                                <input type="text" class="form-control" id ="end_time" name="end_time"  value="<?php echo ($info["end_time"]); ?>">                                            
                            </div>                        
                      </div>
                    </div> -->

                    <div class="form-group">
                      <label for="text" class="col-sm-2 control-label">是否显示</label>
                      <div class="col-sm-5">
                        <div class="col-sm-2">
                           <label><input type="radio" name="is_show" value="1"<?php if($info[is_show] == 1): ?>checked="checked"<?php endif; ?>> 显示 </label>                          
                        </div>
                         <div class="col-sm-3">
                           <label><input type="radio" name="is_show" value="0"<?php if($info[is_show] == 0): ?>checked="checked"<?php endif; ?>> 不显示</label>                          
                        </div>
                      </div>
                    </div>    
                    <div class="form-group">
	                    <label class="control-label col-sm-2">课程描述</label>
	                    <div class="col-sm-8">
			                  <textarea class="form-control" id="post_description" name="description" title=""><?php echo ($info["description"]); ?></textarea>
	                    </div>
                      </div>                        
                    <div class="form-group">
	                    <label class="control-label col-sm-2">课程内容</label>
	                    <div class="col-sm-8">
				        <textarea class="span12 ckeditor" id="post_content" name="content" title="">
				            <?php echo ($info["content"]); ?>
				        </textarea>
	                    </div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-2">              
	                  	 	<input type="hidden" name="activity_id" value="<?php echo ($info["activity_id"]); ?>"></label>
                    	 <div class="col-sm-8">
                    	 	<button type="reset" class="btn btn-default">重置</button>
                    	  	<button type="button"  onclick="checkForm()" class="btn btn-info pull-right">提交</button>
                    	 </div>
                    </div>
                  </div>
                  <div class="box-footer row">

                  </div>
                </form>
              </div>

          </div>
	   </div>
	</section>
</div>

<script type="text/javascript">
    var editor;
    $(function () {
        //具体参数配置在  editor_config.js 中
        var options = {
            zIndex: 999,
            initialFrameWidth: "100%", //初化宽度
            initialFrameHeight: 400, //初化高度
            focus: false, //初始化时，是否让编辑器获得焦点true或false
            maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
            pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
            autoHeightEnabled: true
         /*   autotypeset: {
                mergeEmptyline: true,        //合并空行
                removeClass: true,           //去掉冗余的class
                removeEmptyline: false,      //去掉空行
                textAlign: "left",           //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不执行排版
                imageBlockLine: 'center',    //图片的浮动方式，独占一行剧中,左右浮动，默认: center,left,right,none 去掉这个属性表示不执行排版
                pasteFilter: false,          //根据规则过滤没事粘贴进来的内容
                clearFontSize: false,        //去掉所有的内嵌字号，使用编辑器默认的字号
                clearFontFamily: false,      //去掉所有的内嵌字体，使用编辑器默认的字体
                removeEmptyNode: false,      //去掉空节点
                                             //可以去掉的标签
                removeTagNames: {"font": 1},
                indent: false,               // 行首缩进
                indentValue: '0em'           //行首缩进的大小
            }*/
        };
        editor = new UE.ui.Editor(options);
        editor.render("post_content");
        $.extend({
          myTime: {
              /**
               * 当前时间戳
               * @return <int>        unix时间戳(秒)  
               */
              CurTime: function(){
                  return Date.parse(new Date())/1000;
              },
              /**              
               * 日期 转换为 Unix时间戳
               * @param <string> 2014-01-01 20:20:20  日期格式              
               * @return <int>        unix时间戳(秒)              
               */
              DateToUnix: function(string) {
                  var f = string.split(' ', 2);
                  var d = (f[0] ? f[0] : '').split('-', 3);
                  var t = (f[1] ? f[1] : '').split(':', 3);
                  return (new Date(
                          parseInt(d[0], 10) || null,
                          (parseInt(d[1], 10) || 1) - 1,
                          parseInt(d[2], 10) || null,
                          parseInt(t[0], 10) || null,
                          parseInt(t[1], 10) || null,
                          parseInt(t[2], 10) || null
                          )).getTime() / 1000;
              },
              /**              
               * 时间戳转换日期              
               * @param <int> unixTime    待时间戳(秒)              
               * @param <bool> isFull    返回完整时间(Y-m-d 或者 Y-m-d H:i:s)              
               * @param <int>  timeZone   时区              
               */
              UnixToDate: function(unixTime, isFull, timeZone) {
                  if (typeof (timeZone) == 'number')
                  {
                      unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
                  }
                  var time = new Date(unixTime * 1000);
                  var ymdhis = "";
                  ymdhis += time.getUTCFullYear() + "-";
                  ymdhis += (time.getUTCMonth()+1) + "-";
                  ymdhis += time.getUTCDate();
                  if (isFull === true)
                  {
                      ymdhis += " " + time.getUTCHours() + ":";
                      ymdhis += time.getUTCMinutes() + ":";
                      ymdhis += time.getUTCSeconds();
                  }
                  return ymdhis;
              }
          }
      });
    });  
    
  $('#activity_time').daterangepicker({
    format:"YYYY-MM-DD",
    singleDatePicker: true,
    showDropdowns: true,
    minDate:"<?php echo ($min_date); ?>",
    maxDate:'2030-01-01',
    startDate:"<?php echo ($min_date); ?>",
      locale : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
  });
	$('#start_time').daterangepicker({
		format:"YYYY-MM-DD",
		singleDatePicker: true,
		showDropdowns: true,
		minDate:"<?php echo ($min_date); ?>",
		maxDate:'2030-01-01',
		startDate:"<?php echo ($min_date); ?>",
	    locale : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
	});
  $('#end_time').daterangepicker({
    format:"YYYY-MM-DD HH:mm:ss",
    singleDatePicker: true,
    showDropdowns: true,
    minDate:"<?php echo ($min_date); ?>",
    maxDate:'2030-01-01',
    startDate:"<?php echo ($min_date); ?>",
    timePicker : true, //是否显示小时和分钟  
        timePickerIncrement:1,//time选择递增数
    timePicker12Hour : false, //是否使用12小时制来显示时间  
      locale : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
  });
 
	function checkForm(){
		if($('input[name="title"]').val() == ''){
      layer.msg("请填写课程标题！");
      return false;
    }
    if($('input[name="teacher"]').val() == ''){
      layer.msg("请填写课程讲师！");
      return false;
    }
    if($('input[name="course"]').val() == ''){
      layer.msg("请填写主要课程！");
      return false;
    }
    if($('input[name="activity_address"]').val() == ''){
			layer.msg("请填写课程地址！");
			return false;
		}
		if($('#post_content').val() == ''){
			layer.msg("请填写课程内容！");
			return false;
		}
    if($('#people_num').val() == ''){
      layer.msg("请填写课程人数！");
      return false;
    }
    var at = $('#activity_time').val();
    var st = $('#start_time').val();
    if($.myTime.DateToUnix( at ) > $.myTime.DateToUnix( st )){
      layer.msg("报名截止时间不能大于开课时间");
      return false;
    }
    if($('#post_description').val() == ''){
      layer.msg("请填写课程描述！");
      return false;
    }
    if($('#post_content').val() == ''){
      layer.msg("请填写课程内容！");
      return false;
    }
		$('#add_post').submit();
	}
</script>
</body>
</html>