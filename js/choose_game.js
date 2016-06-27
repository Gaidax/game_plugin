jQuery(function($){
	$(document).ready(function() {
		$('#insert-my-game').click(choose_a_game_window);
	});

	function choose_a_game_window() {
    if (this.window === undefined) {
        this.window = wp.media({
                title: 'Choose a game',
                library: {type: 'image'},
                multiple: false,
                button: {text: 'Insert'}
            });

        var self = this;
        this.window.on('select', function() {
                var first = self.window.state().get('selection').first().toJSON();
                wp.media.editor.insert('[game script="' + first.id + '"]'+ first.id +'[/game]');

/*               for (attr in first){
             	console.log(attr);
                }*/
               
            });
    }

    this.window.open();
    return false;
	}

});