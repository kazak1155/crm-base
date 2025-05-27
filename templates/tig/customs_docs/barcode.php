<script type="text/javascript">
function print_barcode(e,rowid,val,cellName,options,rowObject,gridPseudo,rowObjectFormatted)
{
	var quantity = parseInt(rowObject['ИнСклад_Мест']);
	var inline_quantity = true;
	var changed_quantity = false;
	var grid = this;
	var accepted_box = function()
	{
		$.confirmHTML({
			title:'Подтвердите кол-во позиций',
			yes:'Подтвердить',
			no:'Закрыть',
			width:250,
			html:function()
			{
				var input = $('<input>');
				input.css({
					'width':'calc(100% - 15px)',
					'border':'1px solid #d4d4d4',
					'box-shadow':'inset 0px 2px 2px #ececec',
					'padding':'5px'
				});
				input.val(quantity);
				return input;
			},
			done_func:function()
			{
				var real_quantity = parseInt($('input',this).val());
				if(isNaN(real_quantity) || real_quantity == 0)
					return $.alert('Введено недопустимое значение');
				if(real_quantity != quantity)
					changed_quantity = true;
				accepted(rowid,real_quantity,inline_quantity,changed_quantity);
			}
		})
	};
	var accepted = function(rowid,quantity,inline_quantity,changed_quantity)
	{
		var update = true;
		if(inline_quantity == true && changed_quantity == false)
			update = false;
		if(update == true)
		{
			$.ajaxShort({
				data:{
					action:'edit',
					query:'UPDATE Заказы SET ИнСклад_мест = '+ quantity +' WHERE Код = '+ rowid
				}
			});
		}
		$.ajaxShort({
			dataType:'JSON',
			data:{
				action:'view',
				responseType:'multiple',
				query:'SELECT * FROM Заказы_ШтрихКоды WHERE Заказы_Код = ' + rowid
			},
			success:function(data)
			{
				var barcode_data = data,data_string = new String;
				if(barcode_data.length == 0)
				{
					for(var i = 1; i <= quantity; i++)
					{
						data_string += '('+i+','+rowid+')';
						if(i < quantity)
							data_string += ',';
					}
					warehouse(rowid,quantity,data_string);
				}
				else
				{
					if(quantity < barcode_data.length)
					{
						var row,fall_back = false;
						$.asyncloop({
							length:barcode_data.length,
							loop_action:function(loop,i)
							{
								row = barcode_data[i];
								if(row['Принят'] == 1 && row['Расход_Код'] != null)
								{
									fall_back = true;
									$.alert('Штрихкод '+row['Заказы_Код']+'-'+row['ШтрихКод']+' принят и выдан. Изменение кол-ва мест невозможно.');
									$.ajaxShort({
										data:{
											action:'edit',
											query:'UPDATE Заказы SET ИнСклад_мест = '+ barcode_data.length +' WHERE Код = '+ rowid
										},
										success:function()
										{
											$(grid).trigger("reloadGrid",{current:true});
										}
									});
								}
								else if(row['Принят'] == 1 && row['Расход_Код'] == null)
								{
									$.confirm({
										message:'Штрихкод '+row['Заказы_Код']+'-'+row['ШтрихКод']+' принят. Снять приемку ?',
										done_func:function()
										{
											var dialog = this
											$.ajaxShort({
												data:{
													action:'edit',
													query:'UPDATE Заказы_ШтрихКоды SET Принят = NULL WHERE Заказы_Код = '+ rowid + ' AND ШтрихКод = ' + row['ШтрихКод']
												},
												success:function()
												{
													loop();
												}
											});
										},
										cancel_func:function()
										{
											$(this).dialog('close');
											loop();
										}
									})
								}
								else
								{
									if((i + 1) > quantity)
									{
										$.ajaxShort({
											data:{
												action:'edit',
												query:'DELETE FROM Заказы_ШтрихКоды WHERE Заказы_Код = '+ rowid + ' AND ШтрихКод = ' + row['ШтрихКод']
											},
											success:function()
											{
												loop();
											}
										});
									}
									else
										loop();
								}
							},
							callback:function()
							{
								for(var i = 1; i <= quantity;i++ )
								{
									if(fall_back == true)
										break;
									if(i <= barcode_data.length)
										continue;
									data_string += '('+i+','+rowid+')';
									if(i < quantity)
										data_string += ',';
								}
								warehouse(rowid,quantity,data_string);
							}
						});
					}
					else if(quantity > barcode_data.length)
					{
						for(var i = (barcode_data.length + 1); i <= quantity; i++)
						{
							data_string += '('+i+','+rowid+')';
							if(i < quantity)
								data_string += ',';
						}
						warehouse(rowid,quantity,data_string);
					}
					else if(quantity == barcode_data.length)
						warehouse(rowid,quantity,data_string);
				}
			}
		});
	};
	var warehouse = function(rowid,quantity,data_string)
	{
		if(data_string.length > 0)
		{
			$.ajaxShort({
				data:{
					action:'add',
					query:'INSERT INTO Заказы_ШтрихКоды (ШтрихКод,Заказы_Код) VALUES ' + data_string
				}
			});
		}
		var wh = parseInt(rowObjectFormatted['ИнСклад_Код']);
		if(!isNaN(wh))
			inspection(rowid,quantity,wh);
		else
		{
			$.confirmHTML({
				yes:'Установить',
				no:'Закрыть',
				dialog_opts:{
					open:function()
					{
						dataSelect2.call(this,$('.select2me'),undefined,{allowClear:false});
					}
				},
				html:function()
				{
					var h2 = $('<h3>')
						.html('Не указан Ин.Склад')
						.css({
							'color':'red',
							'width':'100%',
							'text-align':'center'
						});
					var label = $('<label>').html('Ин.Склад:').css('margin-right','15px');
					var select = document.createElement('select');
					select.className = 'select2me';
					select.innerHTML = "<?php echo $this->Core->get_lib_html(['tname'=>'Склады','empty'=>false]); ?>";
					$(select).css('width','calc(100% - 105px)');
					$('option[value="4964"]',select).attr('selected','selected');
					var wrapper = $('<div>');
					wrapper.append(h2).append(label).append(select);
					return wrapper;
				},
				done_func:function()
				{
					wh = $('select',this).val();
					$.ajaxShort({
						data:{
							action:'edit',
							query:'UPDATE Заказы SET ИнСклад_Код = '+ wh +' WHERE Код = '+ rowid
						},
						success:function()
						{
							inspection(rowid,quantity,wh);
						}
					});
				}
			})
		}
	}
	var inspection = function(rowid,quantity,wh)
	{
		$.confirm({
			message:'Передать в терминалы для приемки ?',
			done_func:function()
			{
				$.ajaxShort({
					dataType:'JSON',
					data:{
						action:'view',
						responseType:'column',
						query:'SELECT COUNT(Код) as Amount FROM Склады_Приход WHERE Заказы_Код = '+ rowid +' AND Склады_Код = ' + wh
					},
					success: function(data)
					{
						var query;
						if(data == 0)
							query = "INSERT INTO Склады_Приход (Заказы_Код,Дата_Приход,Дата_Готов,Склады_Код,Мест,Статус_приемки) VALUES ("+ rowid +",'"+ getDate(false,true) +"','"+ getDate(false,true) +"',"+ wh +","+ quantity +",1)";
						else
							query = 'UPDATE Склады_Приход SET Мест = '+ quantity +' WHERE Заказы_Код = '+ rowid +' AND Склады_Код = '+ wh;
						$.ajaxShort({
							data:{
								action:'edit',
								query:query
							},
							success:function()
							{
								$.confirmHTML({
									title:'Параметры печати',
									yes:'Создать',
									no:'Закрыть',
									html:function()
									{
										var wrapper = document.createElement('div');
										$('<div class="float_label_wrapper" style="width:calc(50% - 15px)">')
											.appendTo(wrapper)
											.append("<input type='text' id='width' class='float_input' value='58' required/><label class='float_label' for='Название_RU' for='width'>Ширина</label>");
										$('<div class="float_label_wrapper" style="width:calc(50% - 15px)">')
											.appendTo(wrapper)
											.append("<input type='text' id='height' class='float_input' value='60' required/><label class='float_label' for='height' for='width'>Высота</label>");
										return wrapper;
									},
									done_func:function()
									{
										var height = parseInt($('#height',this).val()),width = parseInt($('#width',this).val());
										if(isNaN(width))
											width = 58;
										if(isNaN(height))
											height = 60;
										$.ajaxShort({
											data:{
												oper:'edit',
												tname:'Заказы',
												id:rowid,
												tid:'Код',
												'Шк_Печатался':'1'
											},
											success:function(){
												$(grid).trigger("reloadGrid",{current:true});
											}
										});

										window.open('/core/file_system/pdf/tig_print_barcode/pdf?id='+ rowid +'&width='+ width +'&height='+ height +'&ref=false','pdf');
									}
								})
							}
						});
					}
				});
			},
			cancel_func:function()
			{
				$.confirmHTML({
					title:'Параметры печати',
					yes:'Создать',
					no:'Закрыть',
					html:function()
					{
						var wrapper = document.createElement('div');
						$('<div class="float_label_wrapper" style="width:calc(50% - 15px)">')
							.appendTo(wrapper)
							.append("<input type='text' id='width' class='float_input' value='58' required/><label class='float_label' for='Название_RU' for='width'>Ширина</label>");
						$('<div class="float_label_wrapper" style="width:calc(50% - 15px)">')
							.appendTo(wrapper)
							.append("<input type='text' id='height' class='float_input' value='60' required/><label class='float_label' for='height' for='width'>Высота</label>");
						return wrapper;
					},
					done_func:function()
					{
						var height = parseInt($('#height',this).val()),width = parseInt($('#width',this).val());
						if(isNaN(width))
							width = 58;
						if(isNaN(height))
							height = 60;
						$.ajaxShort({
							data:{
								oper:'edit',
								tname:'Заказы',
								id:rowid,
								tid:'Код',
								'Шк_Печатался':'1'
							},
							success:function(){
								$(grid).trigger("reloadGrid",{current:true});
							}
						});
						window.open('/core/file_system/pdf/tig_print_barcode/pdf?id='+ rowid +'&width='+ width +'&height='+ height +'&ref=false','pdf');
					}
				})
				$(this).dialog("close");
			}
		})
	}
	if(isNaN(quantity))
	{
		inline_quantity = false;
		$.ajaxShort({
			dataType:'JSON',
			data:{
				action:'view',
				responseType:'column',
				query:'SELECT sum(Кол_мест) FROM Заказы_Состав WHERE Заказы_Код ='+rowid
			},
			success: function(data)
			{
				quantity = parseInt(data);
				if(isNaN(quantity))
					quantity = 0;
				accepted_box();
			}
		});
	}
	else
		accepted_box();
}
</script>
