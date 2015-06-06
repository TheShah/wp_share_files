<table> 
    <tr valign="top">
        <th class="metabox_label_column">
            <span class="wpssf-row-title"><?php _e( 'File Type', 'textdomain' )?></span>
        </th>
        <td>
			<label for="meta-radio-one">
            <input type="radio" name="meta-radio" id="meta-radio-one" value="pdf" <?php if ( isset ( $prfx_stored_meta['meta-radio'] ) ) checked( $prfx_stored_meta['meta-radio'][0], 'pdf'); ?>>
            <?php _e( 'PDF', 'textdomain' )?>
			</label>
			<label for="meta-radio-two">
            <input type="radio" name="meta-radio" id="meta-radio-two" value="word" <?php if ( isset ( $prfx_stored_meta['meta-radio'] ) ) checked( $prfx_stored_meta['meta-radio'][0], 'word' ); ?>>
            <?php _e( 'Word', 'textdomain' )?>
			</label>
        </td>
    </tr>
    <tr valign="top">
         <th class="metabox_label_column">
            <span class="wpssf-row-title"><?php _e( 'Share in Service', 'textdomain' )?></span>
        </th>
        <td>
			<label for="meta-cb-facebook">
            <input type="checkbox" name="meta-cb-fb" id="meta-cb-facebook" value="yes" <?php if ( isset ( $prfx_stored_meta['meta-cb-fb'] ) ) checked( $prfx_stored_meta['meta-cb-fb'][0], 'yes'); ?>>
            <?php _e( 'Facebook', 'textdomain' )?>
			</label>
			<label for="meta-cb-twitter">
            <input type="checkbox" name="meta-cb-tw" id="meta-cb-twitter" value="yes" <?php if ( isset ( $prfx_stored_meta['meta-cb-tw'] ) ) checked( $prfx_stored_meta['meta-cb-tw'][0], 'yes' ); ?>>
            <?php _e( 'Twitter', 'textdomain' )?>
			</label>
        </td>
    </tr>
	<tr valign="top">
		 <th class="metabox_label_column">
            <label for="meta_image">File</label>
        </th>
		<td>
			<input type="text" name="meta_image" id="meta_image" value="<?php if ( isset ( $prfx_stored_meta['meta_image'] ) ) echo $prfx_stored_meta['meta_image'][0]; ?>" />
			<input type="button" id="meta-image-button" class="button" value="<?php _e( 'Choose or Upload an File', 'prfx-textdomain' )?>" />
		</td>
	</tr>
</table>