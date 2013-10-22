/**
 * @load Views.Abstract
 * @load Libs.Event 
 * @load Views.ItemsContainer
 */

Views.Window = Views.Abstract.extend({
	
	_event: null,
	_results_height: null,
	
	initialize: function(){
		this._event = new Libs.Event();
		this._render();
		this._el.scroll($.proxy(this._onScroll, this));
	},
	
	_render: function(){
		this._el = $(window);
	},
		
	onReachFooter: function(callback){
		this._event.add("reach-footer", callback);
	},
	
	unsetReachFooter: function(){
		this._event.remove("reach-footer");
	},
	
	_onScroll: function(){
		var container_height = Views.ItemsContainer.getInstance().getHeight();
		var fake_top = this._el.scrollTop() + this._el.height();
		
		if (fake_top >= container_height){
			this._event.trigger("reach-footer");
			return;
		}
	}
});

create_singleton(Views.Window);


