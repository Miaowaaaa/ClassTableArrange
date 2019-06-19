// JavaScript Document
$(function () {
    //1.初始化Table
    var oTable = new gTableInit();
    oTable.Init();
    //2.初始化Button的点击事件
    var oButtonInit = new gButtonInit();
    oButtonInit.Init();
});
var gTableInit = function(){
	var oTableInit = new Object();
	
	oTableInit.Init = function(){
		$('#g_table').bootstrapTable({
			url:'getGrade.php',
			method: 'get',
      striped: true,    
      cache: false,    
      pagination: true,    
      sortable: true,    
      sortOrder: "asc",    
      pageSize: 10,      
      pageList: [10, 25, 50, 100],
			queryParams: oTableInit.queryParams,
      sidePagination: "client",           //*server时search不起作用。
      search: true,
      idField : "id",
			toolbar:"#toolbar-grade",
			searchOnEnterKey: false,            //回车搜索
			showColumns: true,                  //是否显示所有的列
      showRefresh: true,                  //是否显示刷新按钮
			columns:[{
				checkbox:true
			},{
				field:'rid',
				title:'序号',
				align:'center'
			},{
				field:'id',
				title:'ID',
				align:'center'
			},{
				field:'name',
				title:'年级',
				align:'center'
			},{
				field:'subs',
				title:'课程',
				align:'center'
			},
			{
				field:'cnum',
				title:'班级总数',
				align:'center'
			},{
				field:'dnum',
				title:'上课天数（每周）',
				align:'center'
			},{
				field:'snum',
				title:'节数（每天）',
				align:'center'
			},{
				field:'mtime',
				title:'班会时间',
				align:'center'
			},{
				field:'arrange',
				title:'进行排课',
				align:'center'
			}]
		});
		setTimeout(function () {
       $('#g_table').bootstrapTable('resetView');
    }, 200);
	};
	oTableInit.queryParams = function(params){
		var temp = {
			pageSize:params.limit,
			pageNumber:params.offset,
			key:params.search
		};
		return temp;
	};
	return oTableInit;
};
var gButtonInit = function(){
	var oInit = new Object();
	var postdata = {};
	oInit.Init = function(){
		$('#g_remove').click(function() {
    	var ids="";
    	var c = $('#g_table').bootstrapTable('getAllSelections');
			for(var i = 0;i<c.length;i++){
				ids += "'"+c[i].id+"',";
			}
			ids = ids.substring(0,ids.length-1);
			$.ajax({
				url:'deleteGrade.php',
				data:{'g_ids':ids},
				dataType:"json",
				type:"POST",
				async:true,
				success: function(data){
					if(data.status_g == 'ok' &&  data.status_s == 'ok' && data.status_c == 'ok'){
						var idss = $.map($('#g_table').bootstrapTable('getSelections'),function(row){
							return row.id;
						});
						$('#g_table').bootstrapTable('remove',{
							field:'id',
							values:idss
						});
						$('#c_table').bootstrapTable('refresh',{url:'getClass.php',silent:true});
					}
				}
			});
    });
		$('#g_add').bind('click',function(){ //添加按钮点击后重置输入框
			$('#gid').val('');
			$('#gname').val('');
			$('#per_day').val('');
			$('#per_week').val('');
			$('#gsubs').val('');
			$('#mtime').val('');
			$('#cnum').removAttr("readOnly");
			$('#cnum').val('');
		  $('#g_addBtn').text('确认');
		});
		$('#g_addBtn').click(function(){
			var gid = $('#gid').val();             //年级id
			var gname = $('#gname').val();         //年级名称
			var per_day = $('#per_day').val();     //一天几节课
			var per_week = $('#per_week').val();   //一周几天
			var gsubs = $('#gsubs').val();         //年级所上课程
			var mtime = $('#mtime').val();         //班会时间
			var cnum = $('#cnum').val();           //班级个数
			if($('#g_addBtn').text() == "确认" && gid!=""){   //condition
				$.ajax({
					url:'addGrade.php',
					type:"POST",
					data:{'gid':gid,'gname':gname,'per_day':per_day,'per_week':per_week,'gsubs':gsubs,'mtime':mtime               ,'cnum':cnum},
					dataType:"json",
					asyn:true,
					success: function(data){
						if(data.status_g == 'ok' && data.status_s == 'ok'){
							$('#g_table').bootstrapTable('refresh',{url:'getGrade.php',silent:true});
						}
					}
				});
			}else if(gid !=""){
				$.ajax({
					url:'rewriteGrade.php',
					type:"POST",
					data:{'gid':gid,'gname':gname,'per_day':per_day,'per_week':per_week,'gsubs':gsubs,'mtime':mtime               ,'cnum':cnum},
					dataType:"json",
					async:true,
					success: function(data){
						if(data.status_g == 'ok' && data.status_s == 'ok'){
							$('#g_table').bootstrapTable('refresh',{url:'getGrade.php',silent:true});
						}
					}
				});
			}
		});
		$('#g_edit').popover({
			title:'警告',
			content:'请选择一个要编辑的年级',
			placement:'top',
			trigger:'manual'
		});
		$('#g_subs').click(function(){
			$('#s_selBtn').val('grade');
			$('#selectSubjectModal').modal('show');
		});
		$('#g_edit').click(function(){ 
			if($('#g_table').bootstrapTable('getSelections').length!=1){  //选中一个进行弹出模态框编辑保存数据库
				$('#g_edit').popover('show');
				setTimeout(function(){
					$('#g_edit').popover('hide');
				},1000);
			}else{
				$('#gradeModal').modal('show');
				$('#g_addBtn').text("修改");
				$('#gid').val(g_getSelectedId());
				$('#gname').val(g_getSelectedName());
				$('#per_day').val(g_getSelectedSnum());
				$('#per_week').val(g_getSelectedDnum());
				$('#gsubs').val(g_getSelectedSubs());
				$('#mtime').val(g_getSelectedMtime());
				$('#cnum').val(g_getSelectedCnum());
				$('#cnum').attr("readOnly","true");
			}
		});
		$('#gradModal').on("hidden.bs.modal",function(){
			$('#cnum').attr("readOnly","false");
		});
	};
	return oInit;
};
function g_getSelectedId(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.id;
	});
}
function g_getSelectedName(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.name;
	});
}
function g_getSelectedSubs(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.subs;
	});
}
function g_getSelectedCnum(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.cnum;
	});
}
function g_getSelectedDnum(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.dnum;
	});
}
function g_getSelectedSnum(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.snum;
	});
}
function g_getSelectedMtime(){
	return $.map($('#g_table').bootstrapTable('getSelections'),function(row){
		return row.mtime;
	});
}