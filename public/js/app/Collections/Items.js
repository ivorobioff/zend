/**
 * @load Collections.Abstract
 * @load Models.Item
 */

Collections.Items = Collections.Abstract.extend({
	_model_class: Models.Item,
	_model_primary_key: "ItemId",
	
	_models_array: null,
		
	_counter: 0,
	
	initialize: function(data, silent){
		this._super(data, silent);
		this._models_array = [];
		
		this.onAdd($.proxy(function(model){
			this._models_array.push(model);
		}, this));
	},
	
	eachInBunch: function(callback){
		
		if (this._models_array.length <= 0) return;
		
		for (var i = 0; i < 20; i ++){
			var model = this._models_array.shift();
			this._counter ++;
			callback(model);
			
			if (this._models_array.length <= 0){
				this._event.trigger('last-item');
				break;
			}
		}

		return this;
	},
	
	hasItems: function(){
		return this._models_array.length > 0;
	},
	
	
	getCurrentCount: function(){
		return this._counter;
	},
	
	onLastItem: function(callback){
		this._event.add('last-item', callback);
		return this;
	},
	unsetLastItem: function(){
		this._event.remove('last-item');
	}
});

create_singleton(Collections.Items);