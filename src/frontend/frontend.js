import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import FlyerGallery from './components/FlyerGallery';
import './styles/frontend.scss';

document.addEventListener('DOMContentLoaded', function() {
    const galleries = document.querySelectorAll('.flyer-gallery-root');

    galleries.forEach(gallery => {
        const root = createRoot(gallery);
        const attributes = JSON.parse(gallery.dataset.attributes || '{}');

        root.render(
            <FlyerGallery
                ajaxUrl={window.flyerGalleryData.ajaxurl}
                nonce={window.flyerGalleryData.nonce}
                {...attributes}
            />
        );
    });
});

export const fetchImages = async (page = 1, perPage = 12) => {
    try {
        const params = new URLSearchParams({
            action: 'get_flyer_gallery',
            nonce: window.flyerGalleryData.nonce,
            page: page,
            per_page: perPage
        });

        const response = await fetch(`${window.flyerGalleryData.ajaxurl}?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.data;
    } catch (error) {
        console.error('Error fetching images:', error);
        return { images: [], pages: 0 };
    }
};