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