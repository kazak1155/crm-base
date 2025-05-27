function jqGrid_local_storage$(source)
{
	setter.call(this,source);

	this.first_load = true;
	this.get_Object_from_local_storage();
	this.restoreColumnState();
}
jqGrid_local_storage$.prototype.restoreColumnState = function()
{
	if(this.data)
	{
		for(var i = 0;i< this.col_model.length;i++)
		{
			if(this.col_model[i].name !== 'rn' && this.col_model[i].name !== 'cb')
				this.col_model[i] = $.extend(true,{},this.col_model[i],this.data.colStates[this.col_model[i].name]);
		}
	}
}
jqGrid_local_storage$.prototype.get_Object_from_local_storage = function()
{
	if (typeof window.localStorage !== typeof undefined)
		this.data =  $.parseJSON(window.localStorage.getItem(this.name));
}
jqGrid_local_storage$.prototype.save_Object_in_local_storage = function()
{
	if (typeof window.localStorage !== typeof undefined)
		window.localStorage.setItem(this.name, JSON.stringify(this.data));
}
jqGrid_local_storage$.prototype.remove_Object_from_local_storage = function(reload)
{
	if(typeof window.localStorage !== typeof undefined)
		window.localStorage.removeItem(this.name);
	if(typeof reload !== typeof undefined && reload === true)
		location.reload();
}
jqGrid_local_storage$.prototype.remap_jqGrid_columns = function()
{
	if(this.first_load === true)
	{
		this.first_load = false;
		if(this.data)
		{
			var cm_length = this.col_model.length;
			var col_index;
			if(this.has_sub === true)
				cm_length++
			if(this.data.permutation.length > 0 && this.data.permutation.length < cm_length)
			{
				for(var i = 0;i < this.col_model.length;i++)
				{
					col_index = i;

					if(!this.data.colStates.hasOwnProperty(this.col_model[i].index))
					{
						for(var y = 0;y < this.data.permutation.length;y++)
						{
							if(this.data.permutation[y] >= col_index)
							{
								if(this.has_sub === true)
									col_index++;
								else
									this.data.permutation[y]++;

							}
						}
						this.data.permutation.push(col_index);
					}
				}
				this.grid_element.jqGrid("remapColumns", this.data.permutation, true);
			}
			else if(this.data.permutation.length > 0 && this.data.permutation.length > this.col_model.length)
			{
				for(var i = 0;i < this.data.permutation.length;i++)
				{
					if(typeof this.data.permutation[i] === typeof undefined || this.data.permutation[i] == null)
					{
						this.data.permutation.splice(i,0);
						break;
					}
				}
				this.grid_element.jqGrid("remapColumns", this.data.permutation, true);
			}
			else
			{
				this.grid_element.jqGrid("remapColumns", this.data.permutation, true);
			}
		}
	}
	this.build_jqGrid_local_storage();
}
jqGrid_local_storage$.prototype.build_jqGrid_local_storage = function()
{
	var col_model_item;
	var col_model_name;
	var cur_col_model = this.grid_element_pure.p.colModel;
	var temp_local_storage = {
		sortname:this.grid_element_pure.p.sortname,
		sortorder:this.grid_element_pure.p.sortorder,
		rownum:parseInt(this.grid_element_pure.p.rowNum),
		permutation:this.grid_element_pure.p.remapColumns,
		colStates:{}
	};
	if(this.data && this.data.hasOwnProperty('importState'))
		temp_local_storage.importState = this.data.importState;
	for(var i = 0; i < cur_col_model.length;i++)
	{
		var col_model_data = new Object();
		col_model_item = cur_col_model[i];
		col_model_name = col_model_item.name;
		if(col_model_name !== 'rn' && col_model_name !== 'cb')
		{
			col_model_item.width = typeof col_model_item.width === typeof undefined ? 100 : col_model_item.width;
			col_model_item.hidden = typeof col_model_item.hidden === typeof undefined ? false : col_model_item.hidden;

			col_model_data.width = col_model_item.width;
			col_model_data.hidden = col_model_item.hidden;
			temp_local_storage.colStates[col_model_name] = col_model_data;
			if(col_model_item.hasOwnProperty('searchoptions') && col_model_item.searchoptions.hasOwnProperty('width'))
				temp_local_storage.colStates[col_model_name].searchoptions = {width:col_model_item.searchoptions.width};
		}
	}
	this.col_model = cur_col_model;
	this.data = temp_local_storage;
	this.save_Object_in_local_storage();
}
jqGrid_local_storage$.prototype.remap_jqGrid_columns_width = function(newwidth,index)
{
	var col_model_name = this.col_model[index].name;
	if(this.data && this.data.colStates.hasOwnProperty(col_model_name))
	{
		this.data.colStates[col_model_name].width = newwidth;
		if(this.col_model[index].hasOwnProperty('stype') == true && this.col_model[index].stype === 'select')
		{
			newwidth = 5*(Math.floor(Math.abs(newwidth/5)));
			if(this.data.colStates[col_model_name].hasOwnProperty('searchoptions'))
				this.data.colStates[col_model_name].searchoptions.width = newwidth;
			else
				this.data.colStates[col_model_name].searchoptions = {width:newwidth};
		}
	}
	this.save_Object_in_local_storage();
}
