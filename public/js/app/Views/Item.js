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