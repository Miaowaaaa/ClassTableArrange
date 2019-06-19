// JavaScript Document
// JavaScript Document
$(function () {
    //1.初始化Table
    var oTable = new cTableInit();
    oTable.Init();
    //2.初始化Button的点击事件
    var oButtonInit = new cButtonInit();
    oButtonInit.Init();
});
var cTableInit = function(){
	var oTableInit = new Object();
	
	oTableInit.Init = function(){
		$('#c_table').bootstrapTable({
			url:'getClass.php',
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
			toolbar:"#toolbar-class",
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
				field:'grade',
				title:'年级',
				align:'center'
			},{
				field:'cname',
				title:'班级',
				align:'center'
			},{
				field:'table',
				title:'课表',
				align:'center'
			}]
		});
		setTimeout(function () {
       $('#c_table').bootstrapTable('resetView');
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
var cButtonInit = function(){
	var oInit = new Object();
	var postdata = {};
	oInit.Init = function(){
		$('#c_remove').click(function() {
    	var ids="";
    	var c = $('#c_table').bootstrapTable('getAllSelections');
			for(var i = 0;i<c.length;i++){
				ids += "'"+c[i].id+"',";
			}
			ids = ids.substring(0,ids.length-1);
			$.ajax({
				url:'deleteClass.php',
				data:{'c_ids':ids},
				dataType:"json",
				type:"POST",
				async:true,
				success: function(data){
					if(data.status == 'ok'){
						var idss = $.map($('#c_table').bootstrapTable('getSelections'),function(row){
							return row.id;
						});
						$('#c_table').bootstrapTable('remove',{
							field:'id',
							values:idss
						});
						$('#g_table').bootstrapTable('refresh',{url:'getGrade.php',silent:true});
					}
				}
			});
    });
		$('#c_edit').popover({
			title:'警告',
			content:'请选择一个要编辑的年级',
			placement:'top',
			trigger:'manual'
		});
		$('#c_edit').click(function(){ 
			if($('#c_table').bootstrapTable('getSelections').length!=1){  //选中一个进行弹出模态框编辑保存数据库
				$('#c_edit').popover('show');
				setTimeout(function(){
					$('#c_edit').popover('hide');
				},1000);
			}else{
//				$('#classModal').modal('show');
//				$('#s_id').val(getSelectedId());
//		    $('#s_name').val(getSelectedName());
//		    $('#s_prim').val(getSelectedPrim);
			}
		});
	};
	return oInit;
};
function getSelectedId(){
	return $.map($('#c_table').bootstrapTable('getSelections'),function(row){
		return row.id;
	});
}
function getSelectedGrade(){
	return $.map($('#c_table').bootstrapTable('getSelections'),function(row){
		return row.grade;
	});
}
function getSelectedCname(){
	return $.map($('#c_table').bootstrapTable('getSelections'),function(row){
		return row.cname;
	});
}