import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    SelectControl,
    RangeControl
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType('flyer-gallery/main', {
    title: __('Flyer Gallery', 'flyer-gallery'),
    icon: 'format-gallery',
    category: 'common',
    attributes: {
        year: {
            type: 'string',
            default: ''
        },
        venue: {
            type: 'string',
            default: ''
        },
        artist: {
            type: 'string',
            default: ''
        },
        performer: {
            type: 'string',
            default: ''
        },
        perPage: {
            type: 'number',
            default: 20
        }
    },

    edit: function({ attributes, setAttributes }) {
        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Gallery Settings', 'flyer-gallery')}>
                        <TextControl
                            label={__('Year Filter', 'flyer-gallery')}
                            value={attributes.year}
                            onChange={(year) => setAttributes({ year })}
                        />
                        <TextControl
                            label={__('Venue Filter', 'flyer-gallery')}
                            value={attributes.venue}
                            onChange={(venue) => setAttributes({ venue })}
                        />
                        <TextControl
                            label={__('Artist Filter', 'flyer-gallery')}
                            value={attributes.artist}
                            onChange={(artist) => setAttributes({ artist })}
                        />
                        <TextControl
                            label={__('Performer Filter', 'flyer-gallery')}
                            value={attributes.performer}
                            onChange={(performer) => setAttributes({ performer })}
                        />
                        <RangeControl
                            label={__('Images per page', 'flyer-gallery')}
                            value={attributes.perPage}
                            onChange={(perPage) => setAttributes({ perPage })}
                            min={10}
                            max={200}
                            step={15}
                        />
                    </PanelBody>
                </InspectorControls>
                <div className="wp-block-flyer-gallery">
                    <div className="flyer-gallery-preview">
                        {__('Flyer Gallery Preview', 'flyer-gallery')}
                        <div className="flyer-gallery-preview-settings">
                            {attributes.year && <span>Year: {attributes.year}</span>}
                            {attributes.venue && <span>Venue: {attributes.venue}</span>}
                            {attributes.artist && <span>Artist: {attributes.artist}</span>}
                            {attributes.performer && <span>Performer: {attributes.performer}</span>}
                            <span>Images per page: {attributes.perPage}</span>
                        </div>
                    </div>
                </div>
            </>
        );
    },

    save: function({ attributes }) {
        return null; // Dynamic rendering on frontend
    }
});