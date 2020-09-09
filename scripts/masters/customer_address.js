$('#bill_sub_district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#bill_sub_district').val(adr[0]);
			$('#bill_district').val(adr[1]);
			$('#bill_province').val(adr[2]);
			$('#bill_postcode').val(adr[3]);
		}
	}
});


$('#bill_district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#bill_district').val(adr[0]);
			$('#bill_province').val(adr[1]);
			$('#bill_postcode').val(adr[2]);
		}
	}
});


$('#bill_province').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#bill_province').val(adr[0]);
			$('#bill_postcode').val(adr[1]);
		}
	}
})
