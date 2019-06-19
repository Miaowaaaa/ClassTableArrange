// JavaScript Document
$(function () {
    //1.初始化Table
    var oTable = new sTableInit();
    oTable.Init();
    //2.初始化Button的点击事件
    var oButtonInit = new sButtonInit();
    oButtonInit.Init();
});
var sTableInit = function(){
	var oTableInit = new Object();
	
	oTableInit.Init = function(){
		$('#s_table').bootstrapTable({
			url:'getSubjects.php',
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
			toolbar:"#toolbar",
			searchOnEnterKey: false,            //回车搜索
			showColumns: true,                  //是否显示所有的列
      showRefresh: true,                  //是否显示刷新按钮
			columns:[{
				checkbox:true
			},{
				field:'rid',
				title:'ID',
				align:'center'
			},{
				field:'id',
				title:'编号',
				align:'center'
			},{
				field:'name',
				title:'名称',
				align:'center'
			},{
				field:'prim',
				title:'是否主修',
				align:'center'
			},]
		});
		setTimeout(function () {
       $('#s_table').bootstrapTable('resetView');
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
var sButtonInit = function(){
	var sInit = new Object();
	sInit.Init = function(){
		$('#s_remove').click(function() {
    	var ids = "";
			var c = $('#s_table').bootstrapTable('getAllSelections');
			for(var i = 0;i<c.length;i++){
				ids += "'"+c[i].id+"',";
			}
			ids = ids.substring(0,ids.length-1);
			$.ajax({
				url:'deleteSubject.php',
				data:{'s_ids':ids},
				dataType:"json",
				type:"POST",
				async:true,
				success: function(data){
					if(data.status == 'ok'){
						var idss = $.map($('#s_table').bootstrapTable('getSelections'),function(row){
							return row.id;
						});
						$('#s_table').bootstrapTable('remove',{
							field:'id',
							values:idss
						});
					}
				}
			});
    });
		$('#s_edit').popover({
			title:'警告',
			content:'请选择一个要编辑的课程',
			placement:'top',
			trigger:'manual'
		});
		$('#s_edit').click(function(){ 
			if($('#s_table').bootstrapTable('getSelections').length!=1){  //选中一个进行弹出模态框编辑保存数据库
				$('#s_edit').popover('show');
				setTimeout(function(){
					$('#s_edit').popover('hide');
				},1000);
			}else{
				$('#subjectModal').modal('show');
				$('#s_addBtn').text("修改");
				$('#s_id').val(s_getSelectedId());
		    $('#s_name').val(s_getSelectedName());
		    $('#s_prim').val(s_getSelectedPrim());
			}
		});
		$('#s_addBtn').click(function(){
			var s_id = $('#s_id').val();
			var s_name = $('#s_name').val();
			var s_prim = $('#s_prim').val();
			if(s_id.length>3){
				$('#s_id').css('border-color','#F90B0F');
			}
			else{
				$('#s_id').css('border-color','#ccc');
				if($('#s_addBtn').text() == "确认"){
					$.ajax({			
						url:'addSubject.php',
						type:'POST',
						data:{'s_id':s_id,'s_name':s_name,'s_prim':s_prim},
						dataType:'JSON',
						async:true,
						success:function(data){
							console.log(data.status);
							if(data.status == 'ok'){
								$('#s_table').bootstrapTable('refresh',{url:'getSubjects.php',silent:true});
								$('#s_id').val('');
		    				$('#s_name').val('');
		    				$('#s_prim').val('');
								$('#subjectModal').modal('hide');
							}
						}
			  	});
		  	}else{
					$.ajax({			
						url:'rewriteSubject.php',
						type:'POST',
						data:{'s_id':s_id,'s_name':s_name,'s_prim':s_prim},
						dataType:'JSON',
						async:true,
						success:function(data){
							console.log(data.status);
							if(data.status == 'ok'){
								$('#s_table').bootstrapTable('refresh',{url:'getSubjects.php',silent:true});
								$('#s_id').val('');
		    				$('#s_name').val('');
		    				$('#s_prim').val('');
								$('#subjectModal').modal('hide');
							}
						}
			  	});
		  	}
			}
	  });
	};
	return sInit;
};
function s_getSelectedId(){
	return $.map($('#s_table').bootstrapTable('getSelections'),function(row){
		return row.id;
	});
}
function s_getSelectedName(){
	return $.map($('#s_table').bootstrapTable('getSelections'),function(row){
		return row.name;
	});
}
function s_getSelectedPrim(){
	return $.map($('#s_table').bootstrapTable('getSelections'),function(row){
		return row.prim;
	});
}
