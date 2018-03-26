window.addEvent('load', function() {
	checkColumns();
	$$('select.columns').addEvent('change', function() {
		changeColumns();
	});
});

function changeColumns() {
	checkColumns();
	setColumnParam();
}

function setColumnParam() {
	var widths = new Array();
	$$('div.col').each(function(column){
		widths.push(column.getProperty('id').substring(7)+':'+column.getElement('select').getProperty('value'));
	});

	$('jform[params][columns]').setProperty('value', widths.join(';'));
}

function checkColumns() {
	var widths = new Number(0);
	$$('select.columns').each(function(column){
		widths += parseInt(column.getProperty('value'));
	});
	$('columns_used').set('text', widths);
	if (widths !== window.TPLNUMBER)
	{
		$('column_info').setStyle('color', 'red');
		$('columns_warning').setStyle('display', 'inline');
	}
	else
	{
		$('column_info').setStyle('color', 'inherit');
		$('columns_warning').setStyle('display', 'none');
	}

	$('columnsNumber').set('text', window.TPLNUMBER);
	$('columnsNumber2').set('text', window.TPLNUMBER);

	$$('div.col').each(function(column){
		var col = column.getElement('select').getProperty('value');
		var percent = col*100/window.TPLNUMBER;
		column.setStyle('width', percent + "%");
	});
}

function swapColumns(col, dir) {
	var cols = $$('div.col');
	var index = 0;
	var selected = 'column_'+col;
	if (dir == 'right')
	{
		cols.each(function(el) {
			if (el.getProperty('id') == selected)
			{
				swapindex = index + 1;
			}
			index++;
		});
		$(selected).inject(cols[swapindex],'after');
	}
	else
	{
		cols.each(function(el) {
			if (el.getProperty('id') == selected)
			{
				swapindex = index - 1;
			}
			index++;
		});
		$(selected).inject(cols[swapindex],'before');
	}
	checkColumns();
	setColumnParam();
}

function changeColumnsNumber(columnsNumber) {
	window.TPLNUMBER = columnsNumber;
	checkColumns();
	setColumnParam();
}
