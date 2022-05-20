( function( mw, $ ) {
	$( function() {
		var $c = $( '#menuEditor-container' );
		if ( !$c.length ) {
			return;
		}

		var data = $c.data();
		console.log( $c.data( 'default' ) );
		ext.menueditor.init.getPanelForPage(
			mw.config.get( 'wgPageName' ), data.menuKey, mw.config.get( 'wgRevisionId' ), data.mode, {
				defaultData: $c.data( 'default' )
			}
		).done( function( panel ) {
			panel.connect( this, {
				saveFail: function( error ) {
					$.prepend( new OO.ui.MessageWidget( {
						type: 'error',
						text: error
					} ).$element );
				},
				saveSuccess: function() {
					window.location = mw.util.getUrl( mw.config.get( 'wgPageName' ) );
				},
				cancel: function() {
					window.location = mw.util.getUrl( mw.config.get( 'wgPageName' ) );
				}
			} );
			$c.html( panel.$element );
		} );
	} );
}( mediaWiki, jQuery ) );
