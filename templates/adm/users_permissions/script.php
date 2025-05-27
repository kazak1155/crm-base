<script type="text/javascript">
	$(function() {
		new jqGrid$({
			main:true,
			name:'usrs',
			table:'Permissions',
			id:'id',
			tableSort:'ASC',
			navGrid:true,
			filterToolbar:true,
			beforeSubmitCell:true,
			delOpts:true,
			useLs:false,
			formpos:false,
			navGridOptions:{add:true},
			cn:[
				'Код','Модуль/Компонент','Пользователь','Группа'
			],
			cm:[
				{
					name: "id",index:"id",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
				},

				{
					name: "route_id",index:"route_id",width:200,formatter:'select',stype:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Routes','fields'=>['id','module + \'/\' + component']]) ?>
					},
					editoptions:{
						dataInit:function(elem,opts) {
							new jqGrid_aw_combobox$(elem,
								{
									tname:'Routes',
									flds:['id','module + \'/\' + component'],
									sfld:'module + \'/\' + component'
								},opts,this);
						}
					}
				},

				{
					name: "user_id",index:"user_id",width:200,formatter:'select',stype:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Пользователи','fields'=>['Код','Full_name']]) ?>
					},
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Пользователи',flds:['Код','Full_name']})}
					},
					editoptions:{
						dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Пользователи',flds:['Код','Full_name']},opts,this); }
					}
				},

				{
					name: "group_id",index:"group_id",width:200,formatter:'select',stype:'select',
					formatoptions:{
						value:<?php echo $this->Core->get_lib(['tname'=>'Б_Пользователи_Группы']) ?>
					},
					searchoptions:{
						value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Пользователи_Группы'})}
					},
					editoptions:{
						dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Пользователи_Группы'},opts,this); }
					}
				}
			],
			options:{
				shrinkToFit:true,
				cellEdit:true
			}
		})
	})
</script>
