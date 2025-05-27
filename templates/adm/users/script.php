<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'usrs',
		table:'Пользователи',
		id:'Код',
		tableSort:'ASC',
		navGrid:true,
		filterToolbar:true,
		beforeSubmitCell:true,
		delOpts:true,
		useLs:false,
		navGridOptions:{add:true},
		cn:[
			'Код','Логин','Имя/Фамилия','Группа','Email'
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Login",index:"Login",width:250
			},

			{
				name: "Full_name",index:"Full_name",width:250
			},

			{
				name: "Группа_Код",index:"Группа_Код",width:200,formatter:'select',stype:'select',
				formatoptions:{
					value:<?php echo $this->Core->get_lib(['tname'=>'Б_Пользователи_Группы']) ?>
				},
				searchoptions:{
					value:':',dataInit:dataSelect2,attr:{'data-search':JSON.stringify({tname:'Б_Пользователи_Группы'})}
				},
				editoptions:{
					dataInit:function(elem,opts) { new jqGrid_aw_combobox$(elem,{tname:'Б_Пользователи_Группы'},opts,this); }
				}
			},

			{
				name: "Email",index:"Email",width:250,formatter:function(cellvalue, options, rowObject)
				{
					if(cellvalue != null)
						return '<a href="mailto:'+ cellvalue +'">'+ cellvalue +'</a>';
					else
						return '&nbsp;';
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
