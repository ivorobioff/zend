/**
 * Абстрактный класс вьюшек
 */
Views.Abstract = Class.extend({
	_id: null,
	_tag: null,
	_el: null,

	_render: function(){
		if (_.isString(this._id)){
			this._el = $('#' + this._id);
		}else if(_.isString(this._tag)){
			this._el = $(this._tag);
		}
	},
	
	getElement: function(){
		return this._el;
	},
	
	remove: function(){
		this._el.remove();
	}
});
/**
 * @load Views.Abstract
 */
Views.Item = Views.Abstract.extend({
	
	_template: 'one-item',
	_model: null,
	_player: null,
	
	initialize: function(model){
		this._model = model;
		this._render();
	},
	
	_render: function(){
		this._el = $($('#' + this._template).html().render(this._prepareFields()));
		$('#results').append(this._el);
		this._el.find('.play-pauseZ').click($.proxy(this._createAudio, this));
	},
	
	_prepareFields: function(){
		return {
			song_name: this._model.get("ItemName"),
			artist_name: this._model.get("AuthorName"),
			artist_icon: this._model.get("IconRef"),
			price: this._model.get("PriceText"),
			sales: this._model.get("SalesCountText"),
			rating: this._model.get("RatingText"),
			song_url: this._model.get("ItemRef"),
			looped_audio: this._model.get("LoopedAudioText"),
			bpm:this._model.get("BPMText"),
			duration: this._model.get("DurationText"),
			mp3: this._model.get("Mp3Ref"),
			artist_url: this._model.get("AuthorRef"),
			target_url: this._model.get("SourceRef"),
			target_site: this._model.get("SourceName"),
			sample_rate: this._model.get("SampleRateText"),
			bit_rate: this._model.get("BitRateText"),
			category_name: this._model.get("CategoryName")
		};
	},
	
	_createAudio: function(){
		
		if (!_.isNull(this._player)) return;
		
		this._player = audiojs.create(this._el.find('audio')[0], {
	        css: false,
	        preload: true,
	        autoplay: true,
	        createPlayer: {
	          markup: false,
	          playPauseClass: 'play-pauseZ',
	          scrubberClass: 'scrubberZ',
	          progressClass: 'progressZ',
	          loaderClass: 'loadedZ',
	          timeClass: 'timeZ',
	          durationClass: 'durationZ',
	          playedClass: 'playedZ',
	          errorMessageClass: 'error-messageZ',
	          playingClass: 'playingZ',
	          loadingClass: 'loadingZ',
	          errorClass: 'errorZ'
	        }
	      });
		
	    setTimeout($.proxy(function(){this._player.settings.autoplay = false;}, this), 300);
	}
});
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
Models.Abstract = Class.extend({
	
	_data: null,
	_event: null,
	
	initialize: function(data){
		
		if (_.isUndefined(data)) data = {};
		
		this._data = data;
		this._event = new Libs.Event();
	},
		
	get: function(key){
		return this._data[key];
	},
	
	set: function(key, value, silent){
		
		if (_.isUndefined(silent)) silent = false;
		
		if (!silent) this._event.trigger("set:" + key + ":before", [this]);
		this._set(key, value);		
		if (!silent) this._event.trigger("set:" + key + ":after", [value, this]);
		return this;
	},
	
	update: function(data, silent)
	{
		if (_.isUndefined(silent)) silent = false;
		
		if (!silent) this._event.trigger("update:before", [this]);
	
		for(var i in data){
			this._set(i, data[i]);
		}
		
		if (!silent) this._event.trigger("update:after", [this]);
		return this;
	},
	
	getAll: function(){
		return this._data;
	},
	
	onUpdate: function(callback){
		if (!_.isFunction(callback)){
			this._event.add("update:before", callback.before);
			this._event.add("update:after", callback.after);
		} else {
			this._event.add("update:after", callback);
		}
		return this;
	},
	
	onSet: function(key, callback){
		if (!_.isFunction(callback)){
			this._event.add("set:" + key + ":before", callback.before);
			this._event.add("set:" + key + ":after", callback.after);
		} else {
			this._event.add("set:" + key + ":after", callback);
		}
		
		return this;
	},
	
	_set: function(key, value){
		this._data[key] = value;
	}
});
/**
 * @load Models.Abstract
 */
Models.Item = Models.Abstract.extend({});

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
