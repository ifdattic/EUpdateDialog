/**
 * EUpdateDialog extension file.
 *
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 2.0
 */

/**
 * UpdateDialog object.
 */
var updateDialog = {
  /**
   * @var string CSRF token for CSRF validation.
   */
  csrfToken : '',
  
  /**
   * @var string CSRF token name for CSRF validation.
   */
  csrfTokenName : null,
  
  /**
   * @var string Default title to use then data attribute is not set.
   */
  defaultTitle : 'Dialog',
  
  /**
   * @var int The timeout for callback function in milliseconds.
   */
  timeout: 1000,
  
  /**
   * Add loaded contents to UpdateDialog.
   * @param string url the url of the page to load. 
   */
  addContent : function( url ){
    // Make an AJAX call to get contents
    $.ajax({
      'url': url,
      'data': this.csrfToken,
      'type': 'post',
      'dataType': 'json',
      'success': function( data ){
        // Remove loading indicator
        updateDialog.removeLoader();
        
        // Add returned contents to UpdateDialog
        updateDialog.dialogContent.html( data.content );
      },
      'cache': false
    });
  },
  
  /**
   * Add loading indicator for feedback.
   */
  addLoader : function(){
    this.dialogContent.html( 'Loading...' );
  },
  
  /**
   * Set callback function based on status.
   * @param string status the status returned by form submit.
   */
  callback : function( status ){
    // Switch between callback status
    switch( status )
    {
      // Callback on render
      case 'render':
        setTimeout( this.renderCallback, this.timeout );
        break;
      
      // Callback on success
      case 'success':
        setTimeout( this.successCallback, this.timeout );
        break;
      
      // Callback on delete
      case 'deleted':
        setTimeout( this.deletedCallback, this.timeout );
        break;
      
      // Callback on cancel
      case 'canceled':
        setTimeout( this.canceledCallback, this.timeout );
        break;
      
      // Callback on image delete
      case 'imagedeleted':
        setTimeout( this.imageDeletedCallback, this.timeout );
        break;
      
      // Callback on image delete
      default:
        setTimeout( this.defaultCallback, this.timeout );
        break;
    }
  },
  
  /**
   * Callback then submit was cancelled.
   */
  canceledCallback : function(){
    // Close UpdateDialog
    updateDialog.close();
  },
  
  /**
   * Clean the contents of UpdateDialog.
   */
  cleanContents : function(){
    // Empty UpdateDialog contents.
    this.dialogContent.empty();
  },
  
  /**
   * Close UpdateDialog.
   */
  close : function(){
    // Clean UpdateDialog contents
    this.cleanContents();
    
    // Close UpdateDialog
    this.dialog.dialog( 'close' );
  },
  
  /**
   * Default callback.
   */
  defaultCallback : function(){
    // Close UpdateDialog
    updateDialog.close();
  },
  
  /**
   * Callback the delete was successful.
   */
  deletedCallback : function(){
    // Redirect if URL is set
    if( typeof updateDialog.redirectToAfterDelete !== 'undefined' )
    {
      window.location.replace( updateDialog.redirectToAfterDelete );
    }
  },
  
  /**
   * Get CSRF token for CSRF validation.
   * @return string CSRF token if CSRF validation is enabled.
   */
  getCsrfToken : function(){
    if( ( jQuery.cookie ) && ( this.csrfTokenName != null ) )
    {
      return ( '&' + this.csrfTokenName + '=' + $.cookie( this.csrfTokenName ) );
    }
  },
  
  /**
   * Callback if the image delete was successful.
   */
  imageDeletedCallback : function(){
    // Close UpdateDialog
    updateDialog.close();
  },
  
  /**
   * Initialize UpdateDialog.
   */
  init : function(){
    // Set dialog
    this.dialog = $( '#update-dialog' );
    
    // Get default dialog width
    this.defaultWidth = this.dialog.dialog( 'option', 'width' );
    
    // Set dialog content
    this.dialogContent = this.dialog.children( '.update-dialog-content' );
    
    // Set CSRF token
    this.csrfToken = this.getCsrfToken();
    
    // Attach a handler for all submit buttons in dialog content
    $( '.update-dialog-content input[type=submit]' )
      .live( 'click', function( e ){
        // Prevent default action
        e.preventDefault();
        
        // Submit form data together with clicked button name
        updateDialog.submit( $( this ).attr( 'name' ) );
      });
    
    // Attach a handler for all cancel buttons in dialog content
    $( '.update-dialog-content .update-dialog-cancel-button' )
      .live( 'click', function( e ){
        // Prevent default action
        e.preventDefault();
        
        // Close the UpdateDialog
        updateDialog.close();
      });
  },
  
  /**
   * Open UpdateDialog and load contents for it.
   * @param string url the href attribute value of clicked link.
   */
  open : function( url ){
    // Clean UpdateDialog contents.
    this.cleanContents();
    
    // Use default title for UpdateDialog if it's not set
    if( typeof this.title === 'undefined' )
    {
      this.title = this.defaultTitle;
    }
    
    // Change dialog width if it's bigger than window width
    var width = $( window ).width();
    if( width <= this.defaultWidth ) {
      width -= 40;
    }
    else {
      width = this.defaultWidth;
    }
    
    // Open jQuery UI dialog
    this.dialog.dialog({ title: this.title, width: width }).dialog( 'open' );
    
    // Add loading indicator for feedback
    this.addLoader();
    
    // Add the contents to UpdateDialog
    this.addContent( url );
  },
  
  /**
   * Remove loading indicator.
   */
  removeLoader : function(){
    this.dialogContent.empty();
  },
  
  /**
   * Callback on render.
   */
  renderCallback : function(){},
  
  /**
   * Submit form from UpdateDialog.
   * @param string submitName the name parameter value of the clicked button.
   */
  submit : function( submitName ){
    // Set full submit name
    submitName = '&' + submitName + '=true';
    
    // Get form from UpdateDialog
    var form = this.dialogContent.find( 'form' );
    
    // Get form data
    var formData = form.serialize() + submitName;
    
    // Add loading indicator for feedback
    this.addLoader();
    
    // Make an AJAX call to submit form data
    $.ajax({
      'url': form.attr( 'action' ),
      'data': formData,
      'type': 'post',
      'dataType': 'json',
      'success': function( data ){
        // Remove loading indicator
        updateDialog.removeLoader();
        
        // Add returned contents to UpdateDialog
        updateDialog.dialogContent.html( data.content );
        
        // Run the callback function
        updateDialog.callback( data.status );
      },
      'cache': false
    });
  },
  
  /**
   * Callback then submit was successful.
   */
  successCallback : function(){
    // Update all gridviews
    $( '.grid-view' ).each( function(){
      $.fn.yiiGridView.update( $( this ).attr( 'id' ) );
    });
    
    // Update all listviews
    $( '.list-view' ).each( function(){
      $.fn.yiiListView.update( $( this ).attr( 'id' ) );
    });
    
    // Close UpdateDialog
    updateDialog.close();
  }
};

/**
 * Open UpdateDialog for clicked link.
 * @param object e the event for clicked link.
 */
function updateDialogOpen( e ){
  // Prevent default action
  e.preventDefault();
  
  // Set title for dialog using data attribute
  updateDialog.title = $( this ).data( 'update-dialog-title' );
  
  // Initialize update dialog if it's the first run
  if( typeof updateDialog.dialog === 'undefined' )
  {
    updateDialog.init();
  }
  
  // Open update dialog
  updateDialog.open( $( this ).attr( 'href' ) );
}