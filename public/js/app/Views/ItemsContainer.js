/**
 * @load Views.Abstract
 */

Views.ItemsContainer = Views.Abstract.extend({
	_id: 'results',
	
	initialize: function(){
		this._render();
	},
	
	getHeight: function(){
		return this._el.height();
	}
});

create_singleton(Views.ItemsContainer);
