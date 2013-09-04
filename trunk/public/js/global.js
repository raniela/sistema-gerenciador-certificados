$(function(){
    tinymce.init({
        selector: "textarea", 
        width: 800,
        height: 350,
        plugins: [
         "advlist autolink link lists preview hr textcolor",
         "searchreplace wordcount visualblocks visualchars insertdatetime nonbreaking textcolor",
         "table contextmenu directionality emoticons paste textcolor"
        ],        
        language : "pt_BR",
        toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link textcolor"
    });
    
    $('.converteLetraMaiscula').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });
    
    $('.numeric').numeric();

    $('.data').mask("99/99/9999");
    $(".data").datepicker({
        dateFormat:'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        nextText: 'Próximo',
        prevText: 'Anterior'
    });       

    $(".telefone").mask('(00) 0000-0000');
    $(".cnpj").mask("99.999.999/9999-99");
    $(".cpf").mask("999.999.999-99");
    $(".cep").mask("99.999-999");
});

$.fn.populateSelectJson = function(data)
{
    /* gera o html das options */
    isdata = true;
    var options = '<option value=""></option>';        
    $.each(data, function(key, val) {
        if($.trim(val) != ''){
            options += '<option value="' + key + '">' + val.toUpperCase() + '</option>';
            if(!isDate(val) && !isDate(reverseDate(val)) && !isDateString(val)){
                isdata = false;
            }
        }
    });
    /* seta as options na combo turma */
    obj = $(this).empty().html(options)
    
    /** só ordena se os valores não forem datas */
    if(!isdata){
        return obj.selectSorting();
    }else{
        return obj;
    }    
}

$.fn.selectSorting = function(){
    var selectedVal = $(this).val();	     
    var my_options = $(this).find("option");
    my_options.sort(function(a,b) {
        if (a.text > b.text) return 1;
        else if (a.text < b.text) return -1;
        else return 0
    })
    $(this).empty().append(my_options);	
    $(this).val(selectedVal);   
}

function isDateString(val){
    val = val.toUpperCase();
    if(val.indexOf('JANEIRO') == 0){
        return true;
    }else if(val.indexOf('FEVEREIRO') == 0){
        return true;
    }else if(val.indexOf('MARÇO') == 0 || val.indexOf('MARCO') == 0){
        return true;
    }else if(val.indexOf('ABRIL') == 0){
        return true;
    }else if(val.indexOf('MAIO') == 0){
        return true;
    }else if(val.indexOf('JUNHO') == 0){
        return true;
    }else if(val.indexOf('JULHO') == 0){
        return true;
    }else if(val.indexOf('AGOSTO') == 0){
        return true;
    }else if(val.indexOf('SETEMBRO') == 0){
        return true;
    }else if(val.indexOf('OUTUBRO') == 0){
        return true;
    }else if(val.indexOf('NOVEMBRO') == 0){
        return true;
    }else if(val.indexOf('DEZEMBRO') == 0){
        return true;
    }
    return false;
}

/** MODAL DE AGUARDE DO SISTEMA */
//override these in your code to change the default behavior and style 
$.blockUI.defaults = { 
    // message displayed when blocking (use null for no message) 
    message:  '<h1>Por favor, aguarde...</h1>', 
 
    // styles for the message when blocking; if you wish to disable 
    // these and use an external stylesheet then do this in your code: 
    // $.blockUI.defaults.css = {}; 
    css: { 
        padding:        0, 
        margin:         0, 
        width:          '30%', 
        top:            '40%', 
        left:           '35%', 
        textAlign:      'center', 
        color:          '#000', 
        border:         '3px solid #aaa', 
        backgroundColor:'#fff', 
        cursor:         'wait' 
    }, 
 
    // styles for the overlay 
    overlayCSS:  { 
        backgroundColor: '#000', 
        opacity:         0.6 
    }, 
 
    // styles applied when using $.growlUI 
    growlCSS: { 
        width:    '350px', 
        top:      '10px', 
        left:     '', 
        right:    '10px', 
        border:   'none', 
        padding:  '5px', 
        opacity:   0.6, 
        cursor:    null, 
        color:    '#fff', 
        backgroundColor: '#000', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius':    '10px' 
    }, 
     
    // IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w 
    // (hat tip to Jorge H. N. de Vasconcelos) 
    iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank', 
 
    // force usage of iframe in non-IE browsers (handy for blocking applets) 
    forceIframe: false, 
 
    // z-index for the blocking overlay 
    baseZ: 1000, 
 
    // set these to true to have the message automatically centered 
    centerX: true, // <-- only effects element blocking (page block controlled via css above) 
    centerY: true, 
 
    // allow body element to be stetched in ie6; this makes blocking look better 
    // on "short" pages.  disable if you wish to prevent changes to the body height 
    allowBodyStretch: true, 
 
    // enable if you want key and mouse events to be disabled for content that is blocked 
    bindEvents: true, 
 
    // be default blockUI will supress tab navigation from leaving blocking content 
    // (if bindEvents is true) 
    constrainTabKey: true, 
 
    // fadeIn time in millis; set to 0 to disable fadeIn on block 
    fadeIn:  200, 
 
    // fadeOut time in millis; set to 0 to disable fadeOut on unblock 
    fadeOut:  400, 
 
    // time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock 
    timeout: 0, 
 
    // disable if you don't want to show the overlay 
    showOverlay: true, 
 
    // if true, focus will be placed in the first available input field when 
    // page blocking 
    focusInput: true, 
 
    // suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity) 
    applyPlatformOpacityRules: true, 
 
    // callback method invoked when unblocking has completed; the callback is 
    // passed the element that has been unblocked (which is the window object for page 
    // blocks) and the options that were passed to the unblock call: 
    //     onUnblock(element, options) 
    onUnblock: null, 
 
    // don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493 
    quirksmodeOffsetHack: 4 
};

function ShowMsgAguarde(){
    //$('#div-aguarde').css('display', 'block');
    $.blockUI({
        css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: '0.5', 
            color: '#fff' 
        }
    }); 
}

function CloseMsgAguarde(){
    //$('#div-aguarde').css('display', 'none');
    setTimeout($.unblockUI, 10); 
}

function CapitalizeAll(elemId){
    var txt = $('#' + elemId).val();
    $('#' + elemId).val(txt.toUpperCase());
};

/** recebe a data em formato americano e transforma em um numerico */
function dateToNumber(dt){
    dt = dt.replace('-','');
    dt = dt.replace('-','');
    dt = dt.replace('/','');
    dt = dt.replace('/','');
    return parseInt(dt);
}

/** soma um mês na data. Data no formato Brasileiro */
function somaMesData(data, qtdMes){
    data = str_replace('-','/', data);
    data = data.split('/');
    dia = parseInt(data[0], 10);
    mes = parseInt(data[1], 10);
    ano = parseInt(data[2], 10);
	
    for(iM = 1; iM <= qtdMes; iM++){
        mes++;
        if(mes > 12){
            ano = ano + 1;
            mes = 1;
        }
    }
	
    nova_data = lpad(1, dia, '0') + '/' + lpad(1, mes, '0') + '/' + ano;
    dia_novo = dia;
    while(!isDate(reverseDate(nova_data)) && dia_novo > 28){
        dia_novo = dia_novo -1;
        nova_data = lpad(1, dia_novo, '0') + '/' + lpad(1, mes, '0') + '/' + ano;
    }
    return nova_data;
}

/** data no formato americano */
function isDate(value){
    /*var dateRegEx = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/);
    if (dateRegEx.test(value)) {
    	if(value.substring(0,4) == '0000'){
    		return false;
    	}
        return true;
    }
    return false;*/
	
	
    var date=reverseDate(value);
    var ardt=new Array;
    var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    ardt=date.split("/");
    erro=false;
    if ( date.search(ExpReg)==-1){
        erro = true;
    }
    else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30))
        erro = true;
    else if ( ardt[1]==2) {
        if ((ardt[0]>28)&&((ardt[2]%4)!=0))
            erro = true;
        if ((ardt[0]>29)&&((ardt[2]%4)==0))
            erro = true;
    }
    if (erro) {		
        return false;
    }
    return true;
}

/** formata com caracter v informado a esquerda ate que a string atinja o tamanho tam*/
function lpad(tam, n, v){
    while((n + '').length <= tam){
        n = v + n;
    }
    return n;
}

function validarCNPJ(cnpj) {

	cnpj = cnpj.replace(/[^\d]+/g,'');

	if(cnpj == '') return false;
	
	if (cnpj.length != 14)
		return false;

	// Elimina CNPJs invalidos conhecidos
	if (cnpj == "00000000000000" || 
		cnpj == "11111111111111" || 
		cnpj == "22222222222222" || 
		cnpj == "33333333333333" || 
		cnpj == "44444444444444" || 
		cnpj == "55555555555555" || 
		cnpj == "66666666666666" || 
		cnpj == "77777777777777" || 
		cnpj == "88888888888888" || 
		cnpj == "99999999999999")
		return false;
		
	// Valida DVs
	tamanho = cnpj.length - 2
	numeros = cnpj.substring(0,tamanho);
	digitos = cnpj.substring(tamanho);
	soma = 0;
	pos = tamanho - 7;
	for (i = tamanho; i >= 1; i--) {
	  soma += numeros.charAt(tamanho - i) * pos--;
	  if (pos < 2)
			pos = 9;
	}
	resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	if (resultado != digitos.charAt(0))
		return false;
		
	tamanho = tamanho + 1;
	numeros = cnpj.substring(0,tamanho);
	soma = 0;
	pos = tamanho - 7;
	for (i = tamanho; i >= 1; i--) {
	  soma += numeros.charAt(tamanho - i) * pos--;
	  if (pos < 2)
			pos = 9;
	}
	resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
	if (resultado != digitos.charAt(1))
		  return false;
		  
	return true;
   
}

function validarCPF(cpf) {
 
    cpf = cpf.replace(/[^\d]+/g,'');
 
    if(cpf == '') return false;
 
    // Elimina CPFs invalidos conhecidos
    if (cpf.length != 11 || 
        cpf == "00000000000" || 
        cpf == "11111111111" || 
        cpf == "22222222222" || 
        cpf == "33333333333" || 
        cpf == "44444444444" || 
        cpf == "55555555555" || 
        cpf == "66666666666" || 
        cpf == "77777777777" || 
        cpf == "88888888888" || 
        cpf == "99999999999")
        return false;
        
    // Valida 1o digito
    add = 0;
    for (i=0; i < 9; i ++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
     
    // Valida 2o digito
    add = 0;
    for (i = 0; i < 10; i ++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return false;
         
    return true;
}

jQuery.validator.addMethod("soLetras", function(value, element) {
    return this.optional(element) || soLetras(value);
}, "Campo Sigla deve conter apenas letras");


jQuery.validator.addMethod("soNum", function(value, element) {
    return this.optional(element) || soNum(value);
}, "Campo deve conter apenas numeros");


jQuery.validator.addMethod("validarCPF", function(value, element) {
    return this.optional(element) || validarCPF(value);
}, "Forneça um CPF válido");

jQuery.validator.addMethod("validarCNPJ", function(value, element) {
    return this.optional(element) || validarCNPJ(value);
}, "Forneça um CNPJ válido");


function str_replace (search, replace, subject, count) {
    var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
 
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}

function soLetras(valor){
    if (/[^a-z]/gi.test(valor)) {
        return false;
    }
    return true;            
}

function moneyToFloat($vl){
	
    if($.trim($vl) == ''){
        return 0;
    }else{	
        $vl = str_replace('.','',$vl);
        $vl = str_replace(',','.',$vl);
        return parseFloat($vl);
    }
}

function floatToMoney($vl){
    return number_format($vl,2, ',', '.');
}

function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function reverseDate(data){
    data = $.trim(data);
    data = data.replace('-','/');
    data = data.replace('-','/');
    data = data.split('/');
    data = data.reverse();
    data = $.trim(data.join('/'));
    return data;
}

function urlencode (str) {
    str = (str + '').toString();
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}

function urlencodeGet(str){
    str = (str + '').toString();
    str = str.replace(/!/g, '{1}');
    str = str_replace('/', '{2}', str);
    str = str_replace('(', '{3}', str);
    str = str_replace(')', '{4}', str);
    str = str_replace('*', '{5}', str);
    str = str_replace('%', '{6}', str);
    str = str_replace('#', '{7}', str);
    str = str_replace(' ','{}', str);
    
    str = str_replace('"', '{8}', str);
    str = str_replace("'", '{9}', str);
    str = str_replace('=', '{10}', str);        
     
    return str;
}

function soNum(valor){
    if (/[^a-z]/gi.test(valor)) {
        return true;
    }
    return false;
            
}