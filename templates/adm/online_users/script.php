<script type="text/javascript">
$(function() {
	new jqGrid$({
		main:true,
		name:'users_online',
		table:'Current_connections',
		id:'spid',
		orderName:'LoginName',
		tableSort:'ASC',
		filterToolbar:true,
		navGrid:true,
		cn:[
			"SpId","DBName","LoginName","program_name","cmd"
		],
		cm:[
			{
				name: "spid",index:"spid",width:25
			},

			{
				name: "DBName",index:"DBName",width:200
			},

			{
				name: "LoginName",index:"LoginName",width:200
			},

			{
				name: "program_name",index:"program_name",width:200
			},

			{
				name: "cmd",index:"cmd",width:200
			}
		],
		options:{
			shrinkToFit:true
		}
	})
})
</script>
