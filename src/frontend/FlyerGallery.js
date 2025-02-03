import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

const FlyerGallery = ({ attributes, ajaxUrl, nonce }) => {
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [filters, setFilters] = useState({
        year: attributes.year || '',
        venue: attributes.venue || '',
        artist: '',
        performer: ''
    });

    const fetchImages = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                action: 'get_flyer_gallery',
                nonce,
                page: currentPage,
                per_page: attributes.per_page,
                ...filters
            });

            const response = await fetch(`${ajaxUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                setImages(data.data.images);
                setTotalPages(data.data.pages);
            }
        } catch (error) {
            console.error('Error fetching images:', error);
        }
        setLoading(false);
    };

    useEffect(() => {
        fetchImages();
    }, [currentPage, filters]);

    return (
        <div className="flyer-gallery-container">
            {/* Filters */}
            <div className="flyer-gallery-filters">
                {/* Filter components will go here */}
            </div>

            {/* Gallery Grid */}
            <div className="flyer-gallery-grid">
                {loading ? (
                    <div className="loading">Loading...</div>
                ) : (
                    images.map(image => (
                        <div key={image.id} className="flyer-gallery-item">
                            <img src={image.thumbnail} alt="" />
                        </div>
                    ))
                )}
            </div>

            {/* Pagination */}
            <div className="flyer-gallery-pagination">
                {/* Pagination components will go here */}
            </div>
        </div>
    );
};

// Initialize the gallery
document.addEventListener('DOMContentLoaded', () => {
    const galleryRoot = document.getElementById('flyer-gallery-root');
    if (galleryRoot) {
        const attributes = JSON.parse(galleryRoot.dataset.attributes);
        const ajaxUrl = galleryRoot.dataset.ajaxUrl;
        const nonce = galleryRoot.dataset.nonce;

        const root = createRoot(galleryRoot);
        root.render(
            <FlyerGallery 
                attributes={attributes}
                ajaxUrl={ajaxUrl}
                nonce={nonce}
            />
        );
    }
});