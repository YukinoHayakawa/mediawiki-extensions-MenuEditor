( function( mw, $, bs, undefined ) {

	ext.menueditor.api.Api = function( cfg ) {
		cfg = cfg || {};
	};

	OO.initClass( ext.menueditor.api.Api );

	ext.menueditor.api.Api.prototype.ajax = function( path, data, method ) {
		data = data || {};
		var dfd = $.Deferred();

		$.ajax( {
			method: method,
			url: this.makeUrl( path ),
			data: data,
			contentType: "application/json",
			dataType: 'json'
		} ).done( function( response ) {
			if ( typeof response === 'object' && response.success === false ) {
				dfd.reject();
				return;
			}
			dfd.resolve( response );
		} ).fail( function( jgXHR, type, status ) {
			if ( type === 'error' ) {
				dfd.reject( {
					error: jgXHR.responseJSON || jgXHR.responseText
				} );
			}
			dfd.reject( { type: type, status: status } );
		} );

		return dfd.promise();
	};

	ext.menueditor.api.Api.prototype.makeUrl = function ( path ) {
		if ( path.charAt( 0 ) === '/' ) {
			path = path.substring( 1 );
		}
		return mw.util.wikiScript( 'rest' ) + '/menueditor/' + path;
	};

	ext.menueditor.api.Api.prototype.get = function( path, params ) {
		params = params || {};
		return this.ajax( path, params, 'GET' );
	};

	ext.menueditor.api.Api.prototype.post = function( path, params ) {
		params = params || {};
		return this.ajax( path, JSON.stringify( params ), 'POST' );
	};

	ext.menueditor.api.Api.prototype.put = function( path, params ) {
		params = params || {};
		return this.ajax( path, JSON.stringify( params ), 'PUT' );
	};

	ext.menueditor.api.Api.prototype.delete = function( path, params ) {
		params = params || {};
		return this.ajax( path, JSON.stringify( params ), 'DELETE' );
	};
} )( mediaWiki, jQuery, blueSpice );
