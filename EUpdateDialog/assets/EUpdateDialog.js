/*!
 * EUpdateDialog allows to create/update/delete model entry from JUI Dialog.
 * 
 * Copyright 2011, Andrius Marcinkevicius
 * Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 */

/**
 * Close and clean contents of update dialog.
 */
function closeUpdateDialog(){
  $( '#update-dialog' ).dialog( 'close' ).children( ':eq(0)' ).empty();
}

/**
 * Update contents of update dialog.
 * @param url mixed url of the page to display, or false when submitting form.
 * @param act mixed name of clicked button value [important for delete] 
 */
function updateDialog( url, act ){
  // Set action
  var action = '';
  
  // Get form contained inside update dialog
  var form = $( '#update-dialog div.update-dialog-content form' );
  
  // Set CSRF token if CSRF validation is enabled. 
  var csrfToken = '';
  if( jQuery.cookie )
    csrfToken = '&YII_CSRF_TOKEN=' + $.cookie( 'YII_CSRF_TOKEN' );
    
  // When submitting form set variables to ajax call
  if( url === false )
  {
    action = '&action=' + act;
    url = form.attr( 'action' );
  }
  
  // Make ajax call
  $.ajax({
    'url': url,
    'data': form.serialize() + action + csrfToken,
    'type': 'post',
    'dataType': 'json',
    'success': function( data ){
      if( data.status == 'failure' )
      {
        $( '#update-dialog div.update-dialog-content' ).html( data.content );
        $( '#update-dialog div.update-dialog-content form input[type=submit]' )
          .die() // Stop from re-binding event handlers
          .live( 'click', function( e ){ // Send clicked button value
            e.preventDefault();
            updateDialog( false, $( this ).attr( 'name' ) );
        });
      }
      else
      {
        $( '#update-dialog div.update-dialog-content' ).html( data.content );
        if( data.status == 'success' ) // Update all grid views on success
        {
          $( 'div.grid-view' ).each( function(){
            $.fn.yiiGridView.update( $( this ).attr( 'id' ) );
          });
        }
        setTimeout( closeUpdateDialog, 1000 );
      }
    },
    'cache': false
  });
}

/**
 * Open update dialog.
 * @param url string url of the page to display.
 * @param dialogTitle string the title of the dialog.
 */
function updateDialogActionBase( url, dialogTitle ){
  // Clean the contents, just in case there is something left
  $( '#update-dialog' ).children( ':eq(0)' ).empty();
  
  // Add content to update dialog
  updateDialog( url );
  
  // Open the dialog
  $( '#update-dialog' )
    .dialog( { title: dialogTitle } )
    .dialog( 'open' );
}

/**
 * Open dialog for delete action.
 * @param e Event
 */
function updateDialogDelete( e ){
  e.preventDefault();
  updateDialogActionBase( $( this ).attr( 'href' ), 'Delete confirmation' );
}

/**
 * Open dialog for update action.
 * @param e Event
 */
function updateDialogUpdate( e ){
  e.preventDefault();
  updateDialogActionBase( $( this ).attr( 'href' ), 'Update' );
}

/**
 * Open dialog for create action.
 * @param e Event
 */
function updateDialogCreate( e ){
  e.preventDefault();
  updateDialogActionBase( $( this ).attr( 'href' ), 'Create' );
}

jQuery( function($){
  $( 'a.update-dialog-create' ).bind( 'click', updateDialogCreate );
});
