/**
 * @load Libs.Event
 */
Collections.Abstract = Class.extend({
	_model_class: null,
	_models: null,
	
	_event: null,
	_model_primary_key: "id",
	
	initialize: function(data, silent){
		this._models = {};
		this._event = new Libs.Event();
	
		if (_.isArray(data)) this.addBunch(data, silent);
	},
	
	add: function(data, silent){
		
		if (_.isUndefined(silent)) silent = false;
		
		var model = new this._model_class(data);
		this._models[model.get(this._model_primary_key)] = model;
		
		if (!silent) this._event.trigger("add", [model, this]);
		
		return this;
	},
	
	addBunch: function(data, silent){
		for (var i in data){
			this.add(data[i], silent);
		}
		
		return this;
	},
	
	each: function(callback){
		for (var i in this._models){
			callback(this._models[i], i);
		}
		
		return this;
	},
	
	remove: function(id, silent){
		
		if (_.isUndefined(silent)) silent = false;
		
		var model = this._models[id];
		delete this._models[id];
		
		if (!silent) this._event.trigger("remove", [model, this]);
		
		return this;
	},
	
	onAdd: function(callback){
		this._event.add("add", callback);
		return this;
	},
	
	onRemove: function(callback){
		this._event.add("remove", callback);
		return this;
	}
});