/**
 * @load Views.Abstract
 * @load Views.Window
 * @load Collections.Items
 * @load Views.Item
 */

Views.ItemsLoader = Views.Abstract.extend({
	
	_id: 'load-more',
	_strategy: 'initClick',
	
	initialize: function(){
		this._render();
		this._el.find("a").click($.proxy(this._doClick, this));
	},

	_doClick: function(){		
		if (!Collections.Items.getInstance().hasItems()){
			
			var count = Collections.Items.getInstance().getCurrentCount();
			
			post('/application/index/loaditems', {count: count}, {
				success: $.proxy(function(data){
					if (_.isEmpty(data)){
						this._el.hide();
						return ;
					}
					
					Collections.Items.getInstance().addBunch(data);
					this._addViewItems();
					this._setActiveStage();
					
				}, this)
			});
		} else {
			this._addViewItems();
			this._setActiveStage();
		}
		
		return false;
	},
	
	_setInitStage: function(){
		this._el.show();
		Views.Window.getInstance().unsetReachFooter();
		Collections.Items.getInstance().unsetLastItem();
	},
	
	_setActiveStage: function(){
		
		this._el.hide();
		
		Views.Window.getInstance().onReachFooter(function(){
			Collections.Items.getInstance().eachInBunch(function(model){
				new Views.Item(model);
			});
		});
		
		Collections.Items.getInstance().onLastItem($.proxy(this._setInitStage, this));
	},
	
	_addViewItems: function(){
		Collections.Items.getInstance().eachInBunch(function(model){
			new Views.Item(model);
		});
	}
}); 

create_singleton(Views.ItemsLoader);