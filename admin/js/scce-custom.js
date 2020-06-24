/**
 * The custom Js codes used in this plugin
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

var editor = {};

jQuery(document).ready(function($) {

    // Add codemirror to shortcode output using wp built in codemirror
	if($('#scce-output').length)
		editor = wp.codeEditor.initialize($('#scce-output'), scceCMSettings);

	// Add new atts list item for shortcode (shortcode add edit form)
	var rptContainer	= $('.scce-atts-fields-parent');
	var countItem		= $('.scce-atts-fields').length;
	$(".scce-addmore-atts-btn").on('click', function(e){
		e.preventDefault();
		countItem++;

		// the content that should be cloned
		var cloneElem = $(this).closest('.scce-atts-fields').clone();

		// remove the add new button and create cloneData with the remove btn
		$(cloneElem).find('.scce-addmore-atts-btn').remove();
		$(cloneElem).find('.scce-addremove-atts').html('<button class="button scce-remove-atts-btn" type="button"><span class="dashicons dashicons-no"></span></button>');

		// update the counter for the atts specifirer (ATTS_1, ATTS_2 etc)
		$(cloneElem).find('input[name="scce-atts-specifiers[]"]').val('ATTS_'+countItem);
		$(cloneElem).find('input[name="scce-atts-specifiers[]"]').siblings('span').html('ATTS_'+countItem);

		$(cloneElem).appendTo(rptContainer);
		return false;
	});

	// Remove att list item for shortcode (shortcode add edit form)
	$(document).on("click", ".scce-addremove-atts .scce-remove-atts-btn" , function(e) {
		e.preventDefault();

		// get the confirm text
		var confText = $('#remove-confirm').html();
		var confirmation = confirm(confText);

		if (confirmation == true) { // if confirmed
			if( countItem > 1 ) {
				$(this).closest('.scce-atts-fields').remove();
				countItem--;
			}

			// change the specifier values
			var indx = 1;
			$('.scce-atts-fields-parent input[name="scce-atts-specifiers[]"]').each(function(){
				$(this).val('ATTS_'+indx);
				$(this).siblings('span').html('ATTS_'+indx);
				indx++;
			});

			return false;
		} else { // else do nothing
			return false;
		}
	});

	// Remove or show error message on keyup in the mandatory fields
	$('.scce-mandatory').keyup(function () {
		if(this.value === ''){
			$(this).addClass('scce-invalid');
			$(this).closest('td').find('span.error').show();
		} else{
			$(this).removeClass('scce-invalid');
			$(this).closest('td').find('span.error').hide();
		}
	});

	/**
	 * Need to write the code for the codemirror textarea
	 * to show or remove the error message on keyup
	 */
	$('.CodeMirror').keyup(function () {
		if($(this).siblings('.scce-mandatory').length) {
			var cmValue = editor.codemirror.getValue();
			//if($.trim(cmValue.replace('/\s+/', ' ')) === ''){
			if($.trim(cmValue) === ''){
				$(this).addClass('scce-invalid');
				$(this).closest('td').find('span.error').show();
			} else{
				$(this).removeClass('scce-invalid');
				$(this).closest('td').find('span.error').hide();
			}
		}
	});
	/*-----------*/

	// Toggle list table rows on small screens.
	$( 'tbody' ).on( 'click', 'toggle-row', function() {
		$( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
	});
});

//===========::FORM VALIDATION::===============
function scceValidateForm(form, location) {
	var err = false;
	var reqFields = jQuery(form).find(".scce-mandatory");

	// check for empty fields:
	for (i = 0; i < reqFields.length; i++) {
		/**
		* if text/select field is empty (for SELECT field VALUE is 0)
		* && not a textarea
		*/
		if ( ( reqFields[i].value == '' || (reqFields[i].value == 0 && reqFields[i].tagName.toUpperCase() == 'SELECT' ) ) && reqFields[i].tagName.toUpperCase() !== 'TEXTAREA' ) {
			err = true;
			// Display error alert message or show error message as html
			if ( jQuery(reqFields[i]).attr('type') == 'file' )
				alert("Please upload image file(s).");
			else if ( jQuery( reqFields[i] ).attr( 'type' ) == 'hidden' )
				alert('Please complete the required fields');
			else if ( reqFields[i].tagName.toUpperCase() == 'SELECT' )// if select field but no initial value:
				alert('Please complete selecting [ ' + jQuery(form).find("label[for='" + reqFields[i].name + "']").text() + ' ]');
			else {
				jQuery( reqFields[i] ).addClass( 'scce-invalid' );
				jQuery( reqFields[i] ).closest( 'td' ).find( 'span.error' ).show();
			}
		} else if ( reqFields[i].tagName.toUpperCase() == 'TEXTAREA' ) { // if textarea field is empty
			// if the textarea is used for the codemirror
			if(jQuery(reqFields[i]).siblings('.CodeMirror').length) {
				var cmValue = editor.codemirror.getValue();
				//if(jQuery.trim(cmValue.replace('/\s+/', ' ')) === ''){
				if( jQuery.trim(cmValue) === '' ){
					err = true;
					jQuery(reqFields[i]).siblings('.CodeMirror').addClass('scce-invalid');
					jQuery(reqFields[i]).siblings('.CodeMirror').closest('td').find('span.error').show();
				}
			} else { // for other textarea which is not used for the codemirror
				if (reqFields[i].value === '') {
					err = true;
					jQuery(reqFields[i]).addClass('scce-invalid');
					jQuery(reqFields[i]).closest('td').find('span.error').show();
				}
			}
		}
	}

	return ( err ) ? false : true;
}