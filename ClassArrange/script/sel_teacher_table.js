// JavaScript Document

$(function () {
    //1.初始化Table
		//_request.ccnum = $('#c_cnum').val();       //返回数据中的ccnum
    var oTable = new stTableInit();
    oTable.Init();
    //2.初始化Button的点击事件
    var oButtonInit = new stButtonInit();
    oButtonInit.Init();
});
stTableInit = function(){
	var oTableInit = new Object();
	
	oTableInit.Init = function(){
		$('#s_class_t').bootstrapTable({
			url:'getClass.php',
			method: 'get',
      striped: true,    
      cache: false,    
      pagination: true,    
      sortable: true,    
      sortOrder: "asc",    
      pageSize: 6,      
      pageList: [6],
			queryParams: oTableInit.queryParams,
      sidePagination: "client",           //*server时search不起作用。
      search: true,
      idField : "id",
			toolbar:"#c_toolbar",
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
		$('#sub_table').bootstrapTable({
			pagination: false,
			search: false,
			editable:true,
			toolbar:'#toolbar',
			formatNoMatches: function(){
        return "";
    	},
    	formatLoadingMessage: function(){
        return "";
    	},
			onClickCell: function (field, value,row,td) {
				tdn++;
				if(tdn<2){
					var c = parseInt(field.replace(/d/,""));
					var r = parseInt(td[0].parentElement.firstElementChild.innerText);
					oldDid = (r-1)*parseInt(tmpClass.perweek)+c;//计算did
					td[0].style.backgroundColor="#87CEEB";
					tds.push(td);
				}else if(tdn==2){
					td[0].style.backgroundColor="#87CEEB";
					tds.push(td);  //队列尾部追加
					
					var tmp = tds[0].text();            //交换两个表格的内容
					tds[0].text(tds[1].text());
					tds[1].text(tmp);
					
					var c = parseInt(field.replace(/d/,""));
					var r = parseInt(td[0].parentElement.firstElementChild.innerText);
					var newDid = (r-1)*parseInt(tmpClass.perweek)+c;//计算did
				  var subs = tmpClass.subjects;
					for(var i = 0;i<subs.length;i++){
						if(subs[i].did == oldDid){
							subs[i].did = newDid;
							continue;
						}
					  if(subs[i].did == newDid){
							subs[i].did = oldDid;  
							continue;                 
						}
					}
					tds[0][0].style.backgroundColor="";  //清除
					tds[1][0].style.backgroundColor="";  //清除
					tds = []; //清空队列
					tdn = 0;  //重新开始
				}
			},
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
		$('#s_tchs_t').bootstrapTable({
			url:'getSimpleTeacher.php',
			method: 'get',
      striped: true,    
      cache: false,    
      pagination: true,    
      sortable: true,    
      sortOrder: "asc",    
      pageSize: 8,      
      pageList: [8],
			queryParams: oTableInit.queryParams,
      sidePagination: "client",           //*server时search不起作用。
      search: true,
      idField : "id",
			toolbar:"#t_toolbar",
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
			},{
				field:'sname',
				title:'课程',
				align:'center'
			}]
		});
		setTimeout(function () {
       $('#s_tchs_t').bootstrapTable('resetView');
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
stButtonInit = function(){
  var oButtonInit = new Object();
	oButtonInit.Init = function(){
		$('#s_tch').click(function(){
			$('#s_tchs_t').bootstrapTable("uncheckAll");
			$('#s_tchs_t').bootstrapTable('filterBy',{sname:$('#s_name').val()});    //过滤只显示当前课程的老师
			$('#selectTeacherModal').modal('show');
		});
		$('#s_sure').click(function(){
			//生成返回数据中subs项
			_request.subs[_currentBId].sname = $('#s_name').val();                       //对应加入sname项
			_request.subs[_currentBId].snum = $('#s_num').val();                         //对应加入snum项
			_request.subs[_currentBId].isLP = "";                                        //对应加入isLP项
			$('#SubOptModal').modal('hide');
		});
		$('#t_edit').popover({
			title:'提示',
			html:true,
			content:"<div>点击两个课程即可完成交换!确认修改整个课表记得点击<strong>“保存”</strong>按钮！</div>",
			placement:'bottom',
			trigger:'manual'
		});
		$('#t_download').click(function(){
			$.ajax({
				url:'createWord.php',
				data:{'table':JSON.stringify(tmpClass)},
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
		$('#t_edit').bind('click',function(){
			$('#t_edit').popover('show');
			setTimeout(function(){
				$('#t_edit').popover('hide');
			},3000);
		});
		$('#t_saveBtn').click(function(){
			//save data
			(arrange_param.tables)[nowCid] = tmpClass; //保存	
			$('#subTableModal').modal('hide');
		});
		$('#save').click(function(){
			$.ajax({
				url:"saveTables.php",
				type:"POST",
				async:'true',
				data:{'tables':JSON.stringify(arrange_param)},
				dataType:"json",
				beforeSend: function(){
					$('#save').text("正在保存……");
					$('#save').attr({disabled:'disabled'});
				},
				success: function(data){
				  if(data.status == 'ok'){
						$('#tips').val('保存成功')
						$('#save').removeAttr("disabled");
					  $('#save').text("保存");
					}else{
						$('#tips').val('保存失败');
					}
					$('.alert').css('visibility','visible');
						setTimeout(function(){
							$('.alert').css('visibility','hidden');
					},1000);
				}
			});
		});
		$('#t_sureBtn').click(function(){
			$('#subTableModal').modal('hide');
		});
		$('#c_selBtn').click(function(){                                 //点击选择班级
			var cids = "";
			var cnames = "(";
			var cs = $('#s_class_t').bootstrapTable('getAllSelections');   //bug clear
			for(var i = 0;i<cs.length;i++){
				cids = cids + "&" +cs[i].id;
				cnames += cs[i].id.split("-")[1]+",";
			}
			cids = cids.substring(1,cids.length);
			cnames = cnames.substring(0,cnames.length-1) + ")";
			_request.subs[_currentBId].tchs[currentTId].tclass = cids;        //对应加入tclass
			var nowBtn = document.getElementById('tch_div').childNodes[currentTId];
			nowBtn.textContent = nowBtn.name+cnames;
			$('#selectClassModal').modal('hide');
		});
	  $('#t_selBtn').click(function(){                                    //点击选择老师。
			var tids = "";
			var tchs = "";
			var s = $('#s_tchs_t').bootstrapTable('getAllSelections');
			for(var i = 0;i<s.length;i++){
				tchs = tchs + ";" +s[i].name;
				tids = tids + ";" +s[i].id;
				_request.subs[_currentBId].tchs[i] = new Object();
				_request.subs[_currentBId].tchs[i].tid = s[i].id;               //对应加入tid项
				_request.subs[_currentBId].tchs[i].tname = s[i].name;           //对应加入tname        
			}
			tids = tids.substring(1,tids.length);
			tchs = tchs.substring(1,tchs.length);
			$('#s_tchs').val(tids);
			if(tchs.length>=1) oButtonInit.InitTBtn(tchs);                         //生成左侧老师按钮
			$('#selectTeacherModal').modal('hide');
		});
		$('#arrange').click(function(){
			//_request.ccnum = $('#c_cnum').val();
			var data = JSON.stringify(_request);
			$.ajax({
				url:'test2.php',
				data:{'params':data},
				type:"POST",
				dataType:"json",
				async:true,
				beforeSend: function(){
					$('#arrange').text("请稍等……");
					$('#arrange').attr({disabled:'disabled'});
				},
				success: function(data){
					arrange_param = data;
					$('#arrange').removeAttr("disabled");
					$('#arrange').text("制作课表");
					InitTableBtn(data);
				}
			});
			//alert(JSON.stringify(_request));
		});
	}          
	
	
	oButtonInit.InitTBtn = function(names){                                    // 生成老师按钮
		var tag = document.getElementById('tch_div');
		var tch = names.split(';');
	  for(var i =tag.childNodes.length-1;i>=0;i--){
			tag.removeChild(tag.childNodes[i]);
		}                                                                        //清空左侧老师
		for(var i = 0;i<tch.length;i++){
			var btn = document.createElement('button');
			btn.type="button";
	    btn.textContent=tch[i];
			btn.name = tch[i];
	    btn.className="btn btn-info left-menu-item-main";
	    btn.onclick = function(){
				currentTId = $(this).parent().children().index(this);
				$('#s_class_t').bootstrapTable("uncheckAll");
				$('#s_class_t').bootstrapTable("hideColumn",'rid');
				$('#s_class_t').bootstrapTable("hideColumn",'table');
				$('#s_class_t').bootstrapTable("filterBy",{grade:decodeURI(_gname)});
				$('#selectClassModal').modal('show');                                //点击老师按钮,选择教授班级。
	    };
	    tag.appendChild(btn);
		}
	};
	return oButtonInit;
}
function InitTableBtn(data){
		var cids = data.cids;
		for(var i = 0;i<cids.length+1;i++){
			for(var j = i+1;j<cids.length;j++){
				var a = parseInt(cids[i].split('-')[1]);
				var b = parseInt(cids[j].split('-')[1]);
				if(a>b){
					var temp = cids[i];
					cids[i] = cids[j];
					cids[j] = temp;
				}
			}
		}
		var tag = document.getElementById('result-content');                     //面板
		//wait to code 这里需要先清除面板，以防重复显示
		for(var i = tag.childNodes.length-1;i>=0;i--){
			tag.removeChild(tag.childNodes[i]);
		}
		for(var i = 0;i<cids.length;i++){
			var c = document.createElement('div');
			var icon = document.createElement('img');
			var text = document.createElement('div');
			icon.className = 'result-item-icon fade in';
			icon.src = 'Image/timg.png'; 
			text.className = 'result-item-text';
			text.name = cids[i];
			text.textContent = ((data.tables)[cids[i]]).cname;
			c.className ='result-item';
			c.appendChild(icon);
			c.appendChild(text);
			c.onclick = function(){
				//在此处加载表格数据
				nowCid = cids[i];
				AddTableData(data,$(this).children().last().prop('name'));  //班级id保存在name中
				$('#subTableModal').modal('show');
			}
			tag.appendChild(c);
		}
		
}
function AddTableData(data,cid){
		$('#sub_table').bootstrapTable('removeAll');
		var tbs = data.tables;
		var tb = tbs[cid];
		tmpClass = tb;                //赋予临时班级对象数据
		var subs = tb.subjects;
		var snum = tb.perday;
		var dnum = tb.perweek;
		var rows = new Array(snum);
		$('#table_name').text(tb.cname);
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
				var sname = subs[i].sname;
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