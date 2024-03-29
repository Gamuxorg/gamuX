/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import { TextControl, Button } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
const BACKGROUND_COLORS = {
	grey: '#f3f4f5',
    green: '#e9fbe5'
};


// toggleImgDisplay.bind(this);

export default function Edit( { 
    attributes,
	setAttributes,
	insertBlocksAfter,
	isSelected } ) {
    var toggleImgDisplay = () => {
        this.parentNode.parentNode.previousSibling.style.display=(attributes.content=="") ? "none" : "block";
    }

    const onOpenMediauploader = (e) => {
		e.preventDefault();
        var uploader_1 = wp.media( {
            title: 'Upload Image',
            button: {
                text: '选择'
            },
            multiple: false
        }).on( 'select', () => {
            var media = uploader_1.state().get('selection').first().toJSON();
            setAttributes( { content: media.url } );
        }).on( 'error', ( errorMessage ) => {
            console.error( 'Upload Error: ', errorMessage );
        }).open();
    };

    return (
        <div { ...useBlockProps() } >
            <figure class="wp-block-image size-large">
                <img src={ attributes.content } alt=""/>
            </figure>
            <TextControl 
                value={ attributes.content }
                onChange={ function(val) { 
                    setAttributes( { content: val } );
                } }
            />
            <Button 
                isSecondary 
                isSmall
                className="addSlideImage-botton"
                onClick={ onOpenMediauploader }>
                    上传本地图片
            </Button>
            <Button 
                isSecondary 
                isSmall
                className="addSlideUrl-botton"
                onClick={ (e) => {
                    e.preventDefault();
                    insertBlocksAfter( createBlock( 'gamux/slide-url' ) ); 
                } }>
                    添加轮播图URL
            </Button>
        </div>         
    );
}