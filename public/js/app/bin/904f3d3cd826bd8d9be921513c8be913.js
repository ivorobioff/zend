Libs.Event = Class.extend({
	_events: null,
	
	initialize: function(){
		this._events = {};
	},
	
	add: function (event, callback){
		if (_.isUndefined(this._events[event])){
			this._events[event] = [];
		}
		
		this._events[event].push(callback);
	},
	
	trigger: function(event, params){
		
		if (_.isUndefined(this._events[event])) return ; 
		if (_.isUndefined(params)) params = [];	
		
		var events = this._events[event];
		
		for (var i in events){
			events[i].apply(this, params)
		}
	},
	
	remove: function(event){
		if (_.isUndefined(this._events[event])) return ;
		delete this._events[event];
	}
});
/**
 * @load Libs.Event
 */
Libs.HashListener = Class.extend({
	_event: null,
	
	initialize: function(){
		this._event = new Libs.Event();
		$(window).bind("hashchange", $.proxy(function(e){			
			this._event.trigger("hashchange", [location.hash.ltrim("#!/")]);
		}, this));
	},
	
	onChange: function(callback){
		this._event.add("hashchange", callback);
	},
	
	unsetChange: function(alias){
		this._event.remove("hashchange");
	}
});

create_singleton(Libs.HashListener);
