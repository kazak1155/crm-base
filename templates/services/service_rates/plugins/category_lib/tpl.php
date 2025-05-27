<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'goods_categories',
		table:'Категории_груза',
		id:'Код',
		hideBottomNav:true,
		beforeSubmitCell:true,
		useLs:false,
		navGrid:false,
		cn:[
			"Код","Название","Стоимость","Расчет по объему","Расчет по весу"
		],
		cm:[
			{
				name: "Код",index:"Код",width:50,hidden:true,editable:false,editrules:{edithidden:false},hidedlg:true
			},

			{
				name: "Название",index:"Название",width:250
			},

			{
				name: "Стоимость",index:"Стоимость",width:80,formatter:floatFormatter
			},

			{
				name: "Расчет_по_объему",index:"Расчет_по_объему",width:100,gtype:'checkbox'
			},

			{
				name: "Расчет_по_весу",index:"Расчет_по_весу",width:100,gtype:'checkbox'
			},
		],
		options:{
			shrinkToFit:true,
			cellEdit:true
		}
	});
})
</script>
