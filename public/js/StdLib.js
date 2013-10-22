/**
 * @load Libs.Underscore
 */
var Views = {};
var Models = {};
var Collections = {};
var Helpers = {};
var Libs = {};
var i18n = {};


//Добавил метод в объект String
String.prototype.toCamelCase = function(){
	
	var arr = this.split('-');
	var n_str = '';
	
	for (var i in arr){
		n_str += arr[i].charAt(0).toUpperCase() + arr[i].substr(1); 
	}
	
	return n_str.charAt(0).toLowerCase() + n_str.substr(1);
}


String.prototype.replaceAll = function(search_value, new_value){
	return this.replace(new RegExp(search_value, "g"), new_value);
}

String.prototype.render = function(data, modif){
	var html = this + "";
	
	for (var i in data){
		var escape = true;
		
		if (_.isObject(modif) && _.isFunction(modif[i])){
			data[i] = modif[i](data[i], i);
			escape = false;
		}
		
		var data_ = escape ? _.escape(data[i]) : data[i];
		
		html = html.replaceAll('{{' + i + '}}', data_);
	}
	
	return html;
}

String.prototype.ltrim = function (ch){
	var str = this + "";
	return _.string.ltrim(str, ch);
}

String.prototype.rtrim = function (ch){
	var str = this + "";
	return _.string.rtrim(str, ch);
}

String.prototype.trim = function (ch){
	var str = this + "";
	return _.string.trim(str, ch);
}

/**
 * Полезная функция для дебага. Выводит хэш атрибутов объекта
 */
function pred(data){
	alert(JSON.stringify(data));
}

/**
 * Создает синглтон для класа
 * @param class_name
 */
function create_singleton(class_name){
	class_name._INSTANCE = null;
	
	class_name.getInstance = function(){
		
		if (class_name._INSTANCE == null){
			class_name._INSTANCE = new class_name();
		}
		
		return class_name._INSTANCE;
	}
}

function _url(url){
	return url;
}

function post(url, data, callback){
	url = _url(url);
	$.post(url, data, function(result){
		
		if (_.isFunction(callback["callback"])){
			callback.callback(result);
		}
		
		if (result.status == 'success'){
			if (_.isFunction(callback["success"])){
				callback.success(result.data);
			}
		}
		else if(result.status == 'error'){
			if (_.isFunction(callback["error"])){
				callback.error(result.data);
			}
		}else throw 'Wrong response status';
	}, 'json');
}
