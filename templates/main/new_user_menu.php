<style>
body
{
	background:#567;
}
#login
{
	position: fixed;
	top: 50%;
	left: 50%;
	-webkit-transform: translate(-50%, -50%);
	transform: translate(-50%, -50%);
	margin:0 auto;
	margin-top:8px;
	margin-bottom:2%;
	transition:opacity 1s;
	-webkit-transition:opacity 1s;
}
form
{
	width:300px;
	background:#f0f0f0;
	padding:6% 4%;
	border-radius: 5px;
}
.data
{
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	width:100%;
	background:#fff;
	margin-bottom:4%;
	border:1px solid #ccc;
	padding:10px 0px 10px 5px;
	color:#555;
}
input[type="submit"]
{
	width:100%;
	background:#3399cc;
	border:0;
	padding:1%;
	padding:10px 0px 10px 5px;
	color:#fff;
	cursor:pointer;
}
</style>
<div id="login">
	<form method="POST">
		<input class="data" name="name" placeholder="Имя" required="required"/>
		<input class="data" name="s_name" placeholder="Фамилия" required="required"/>
		<input class="data" type="email" name="email" placeholder="Рабочий email" required="required" />
		<input name="newuser" type="hidden"/>
		<input type="submit" value="Сохранить" />
	</form>
</div>
