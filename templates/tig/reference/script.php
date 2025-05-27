<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'libs',
		id:'Код',
		table:'Справочники',
		tableSort:'ASC',
		onCellSelect:true,
		beforeSubmitCell:true,
		filterToolbar:true,
		delOpts:true,
		navGrid:true,
		defaultPage:false,
		libsGrid:true,
		useLs:false,
		cn:['',''],
		cm:[
			{
				name:'Код',hidden:true
			},
			{
				name:'Prepare',resizable:false
			}
		],
		options:{
			shrinkToFit:true,
			autowidth:true,
			cellEdit:true
		}
	})
})
</script>
