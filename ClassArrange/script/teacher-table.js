// JavaScript Document
// JavaScript Document
// JavaScript Document
$(function () {
    //1.初始化Table
    var oTable = new tTableInit();
    oTable.Init();
    //2.初始化Button的点击事件
    var oButtonInit = new tButtonInit();
    oButtonInit.Init();
});
var tTableInit = function(){
	var oTableInit = new Object();
	
	oTableInit.Init = function(){
		$('#sub_table').bootstrapTable({
			pagination: false,
			search: false,
			editable:true,
			formatNoMatches: function(){
        return "";
    	},
    	formatLoadingMessage: function(){
        return "";
    	},
			toolbar:'#subt_toolbar',
			columns:[{
				field:'d0',
				title:"",
				align:'center'
			},{
				field:'d1',
				title:'周一',
				align:'center',
				edit:true
			},{
				field:'d2',
				title:'周二',
				align:'center'
			},{
				field:'d3',
				title:'周三',
				align:'center'
			},{
				field:'d4',
				title:'周四',
				align:'center',
				editable:true,
			},{
				field:'d5',
				title:'周五',
				align:'center'
			},{
				field:'d6',
				title:'周六',
				align:'center'
			},{
				field:'d7',
				title:'周日',
				align:'center'
			}]
		});
		$('#s_subs_t').bootstrapTable({
			url:'simpleSubs.php',
			method: 'get',  
      cache: false,    
      pagination: true,     
      sortOrder: "asc",    
      pageSize: 8,      
      pageList: [8],
			queryParams: oTableInit.queryParams,
      sidePagination: "client",           //*server时search不起作用。
      search: true,
      idField:"id",
			toolbar:"#s_toolbar",
			columns:[{
				checkbox:true
			},{
				field:'id',
				title:'编号',
				align:'center'
			},{
				field:'name',
				title:'名称',
				align:'center'
			}]
		});
		$('#t_table').bootstrapTable({
			url:'getTeacherData.php',
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
			toolbar:"#toolbar-teacher",
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
				field:'tname',
				title:'姓名',
				align:'center'
			},{
				field:'subs',
				title:'教授课程',
				align:'center'
			},{
				field:'cnstr',
				title:'教授约束',
				align:'center'
			},{
				field:'table',
				title:'查看课表',
				align:'center'
			}]
		});
		setTimeout(function () {
       $('#t_table').bootstrapTable('resetView');
			 $('#s_subs_t').bootstrapTable('resetView');
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
var tButtonInit = function(){
	var oInit = new Object();
	oInit.Init = function(){
		$('#t_remove').click(function() {
			var ids="";
    	var c = $('#t_table').bootstrapTable('getAllSelections');
			for(var i = 0;i<c.length;i++){
				ids += "'"+c[i].id+"',";
			}
			ids = ids.substring(0,ids.length-1);
			$.ajax({
				url:'deleteTeacher.php',
				data:{'t_ids':ids},
				dataType:"json",
				type:"POST",
				async:true,
				success: function(data){
					if(data.status_t == 'ok' && data.status_s == 'ok'){
						var idss = $.map($('#t_table').bootstrapTable('getSelections'),function(row){
							return row.id;
						});
						$('#t_table').bootstrapTable('remove',{
							field:'id',
							values:idss
						});
					}
				}
			});
    });
		$('#t_edit').popover({
			title:'警告',
			content:'请选择一个要编辑的年级',
			placement:'top',
			trigger:'manual'
		});
		$('#t_add').bind("click",function(e){
			$('#t_addBtn').text('确认');
			$('#t_id').val('');
			$('#t_name').val('');
		  $('#t_sub').val('');
			$('#t_cnstr').val('');
		});
		$('#selectSubjectModal').on('show.bs.modal',function(e){  //点击时在显示之前刷新表格	
			$('#s_subs_t').bootstrapTable('refresh',{url:'simpleSubs.php',silent:true});
		});
		$('#selectSubjectModal').on('shown.bs.modal',function(e){  //显示之后要勾选已经选择的
			if($('#s_selBtn').val() == 'teacher')
			    var subs = $('#t_sub').val();
			else
			    var subs = $('#gsubs').val();
			var params = {field:'name',values:subs.split(";")};
			$('#s_subs_t').bootstrapTable('checkBy',params);
		});
    $('#t_subs').click(function(){   //打开选择课程模态框
		  $('#s_selBtn').val('teacher');
			$('#selectSubjectModal').modal('show');
		});
		$('#t_download').click(function(){
			$.ajax({
				url:'createWord.php',
				data:{'table':JSON.stringify(tmpTable)},
				dataType:"json",
				type:"POST",
				async:true,
				success: function(data){
					if(data.status == 'ok'){
						var filepath = data.filepath;
						//请求下载
						var form=$("<form>");//定义一个form表单
						form.attr("style","display:none");
						form.attr("target","");
						form.attr("method","get"); //请求类型
						form.attr("action",'download.php'); //请求地址
						$("body").append(form);//将表单放置在web中
						
						var input1=$("<input>");
						input1.attr("type","hidden");
						input1.attr("name","filepath");
						input1.attr("value",filepath);
						form.append(input1);
						form.submit();//表单提交
					}
				}
			});
		});
		$('#s_selBtn').click(function(){   //确定选择
			var names = $('#s_subs_t').bootstrapTable('getAllSelections');
			var t_sub = "";
			for (var i = 0;i<names.length;i++){
				t_sub += names[i].name+";";
			}
			t_sub = t_sub.substring(0,t_sub.length-1);
			$('#selectSubjectModal').modal('hide');
			if($('#s_selBtn').val() == 'teacher')
			    $('#t_sub').val(t_sub);
			else
			    $('#gsubs').val(t_sub);
		});
		
		$('#t_edit').click(function(){ 
			if($('#t_table').bootstrapTable('getSelections').length!=1){  //选中一个进行弹出模态框编辑保存数据库
				$('#t_edit').popover('show');
				setTimeout(function(){
					$('#t_edit').popover('hide');
				},1000);
			}else{
				$('#teacherModal').modal('show');
				$('#t_addBtn').text('修改');
				$('#t_id').val(t_getSelectedId());
				$('#t_name').val(t_getSelectedName());
				$('#t_sub').val(t_getSelectedSubs());
				$('#t_cnstr').val(t_getSelectedCnstr());
			}
		});
		$('#t_addBtn').click(function(){
			var t_id = $('#t_id').val();
			var t_name = $('#t_name').val();
			var t_subs = $('#t_sub').val();
			var t_cnstr = $('#t_cnstr').val();
			if($('#t_addBtn').text() == '确认'){
				$.ajax({
					url:'addTeacher.php',
					type:'POST',
					data:{'t_id':t_id,'t_name':t_name,'t_sub':t_subs,'t_cnstr':t_cnstr},
					dataType:"json",
					async:true,
					success: function(data){
						if(data.status_t == 'ok'&& data.status_s== 'ok'){
							$('#t_table').bootstrapTable('insertRow',{
								index:$('#t_table').bootstrapTable('getOptions').totalRows,
          			row:{
									rid:"1",
									id:t_id,
									tname:t_name,
									subs:t_subs,
									cnstr:t_cnstr,
									table:"<a href='#'>查看课表</a>"
								}
					  	});
							$('#teacherModal').modal('hide');
						}
					}
				});
			}else{   //修改
				$.ajax({
					url:'rewriteTeacher.php',
					type:'POST',
					data:{'t_id':t_id,'t_name':t_name,'t_sub':t_subs,'t_cnstr':t_cnstr},
					dataType:"json",
					async:true,
					success: function(data){
						if(data.status_t == 'ok'&& data.status_s== 'ok'){
							$('#t_table').bootstrapTable('refresh',{url:'getTeacherData.php',silent:true});
							$('#teacherModal').modal('hide');
						}
					}
				});
			}
		});
	};
	return oInit;
};
function t_getSelectedId(){
	return $.map($('#t_table').bootstrapTable('getSelections'),function(row){
		return row.id;
	});
}
function t_getSelectedName(){
	return $.map($('#t_table').bootstrapTable('getSelections'),function(row){
		return row.tname;
	});
}
function t_getSelectedSubs(){
	return $.map($('#t_table').bootstrapTable('getSelections'),function(row){
		return row.subs;
	});
}
function t_getSelectedCnstr(){
	return $.map($('#t_table').bootstrapTable('getSelections'),function(row){
		return row.cnstr;
	});
}
function t_showTable(ele){
	var params = ele.href.split('#')[1];
	var Url = params.split('?')[1];
	var tid = params.split('?')[0];
	$.ajax({
		url:Url,
		async:true,
		data:{'tid':tid},
		type:"POST",
		dataType:"json",
		success: function(data){
			if(data.status == 'ok'){
				tmpTable = data.data;
			  t_createTable(data.data);
			  $('#subTableModal').modal('show');
			}
		}
	});
}
function t_createTable(data){
		$('#sub_table').bootstrapTable('removeAll');
		var subs = data.subjects;
		var snum = data.perday==null?8:data.perday;
		var dnum = data.perweek;
		var rows = new Array(snum);
		for(var i = 0;i<snum;i++){
			rows[i] = new Object();
			rows[i].d0 = i+1;
			rows[i].d1 = "";
			rows[i].d2 = "";
			rows[i].d3 = "";
			rows[i].d4 = "";
			rows[i].d5 = "";
			rows[i].d6 = "";
			rows[i].d7 = "";
		}
		for(var i = 0;i<subs.length;i++){
		    var did = subs[i].did;
				var d = did%dnum == 0?parseInt(dnum):(did%dnum);
				var s = Math.ceil(did/dnum)-1;
				if(subs[i].cname != null){
						var sname = subs[i].sname+"<br>("+subs[i].cname+")";
				}else{
					  var sname = subs[i].sname;
				}
				switch(d){
				  case 1:rows[s].d1 = sname;//+subs[i].
					      break;
					case 2:rows[s].d2 = sname;
					      break; 
					case 3:rows[s].d3 = sname;
					      break; 
					case 4:rows[s].d4 = sname;
					      break; 
					case 5:rows[s].d5 = sname;
					      break; 
					case 6:rows[s].d6 = sname;
					      break; 
					case 7:rows[s].d7 = sname;
					      break; 
				}
		}
		for(var i = 0;i<snum;i++){
			$('#sub_table').bootstrapTable('append',rows[i]);
		}
		
}