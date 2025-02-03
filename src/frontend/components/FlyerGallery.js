import React, { useState, useEffect } from 'react';
import Select from 'react-select';
import Lightbox from './Lightbox';  // Remove the curly braces
import PerformerSelect from './PerformerSelect';

const FlyerGalleryFilters = ({ filters, onChange, availableFilters }) => {
    const createOptions = (values) => {
        return values.map(value => ({ value, label: value }));
    };

    return (
        <div className="flyer-gallery-filters">
            <label htmlFor="year-filter">Year</label>
            <Select
                id="year-filter"
                className="filter-select"
                value={filters.year ? { value: filters.year, label: filters.year } : null}
                onChange={(option) => onChange({ ...filters, year: option ? option.value : '' })}
                options={createOptions(availableFilters.years)}
                isClearable
                placeholder="Select Year"
            />

            <label htmlFor="venue-filter">Venues</label>
            <Select
                id="venue-filter"
                className="filter-select"
                value={filters.venue ? { value: filters.venue, label: filters.venue } : null}
                onChange={(option) => onChange({ ...filters, venue: option ? option.value : '' })}
                options={createOptions(availableFilters.venues)}
                isClearable
                placeholder="Select Venue"
            />

            <PerformerSelect
                value={filters.performer}
                onChange={(value) => onChange({ ...filters, performer: value })}
                performers={availableFilters.performers}
            />
        </div>
    );
};

const FlyerGalleryPagination = ({ currentPage, totalPages, onPageChange, perPage, onPerPageChange }) => {
    return (
        <div className="flyer-gallery-pagination">
            <div className="per-page-select">
                <select
                    value={perPage}
                    onChange={e => onPerPageChange(parseInt(e.target.value))}
                >
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="48">50</option>
                    <option value="100">100</option>
                </select>
                <span>per page</span>
            </div>

            <div className="pagination-controls">
                {currentPage > 1 && (
                    <button onClick={() => onPageChange(currentPage - 1)}>
                        Previous
                    </button>
                )}
                <span>{currentPage} / {totalPages}</span>
                {currentPage < totalPages && (
                    <button onClick={() => onPageChange(currentPage + 1)}>
                        Next
                    </button>
                )}
            </div>
        </div>
    );
};

export const FlyerGallery = ({ rootElement }) => {
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [selectedImage, setSelectedImage] = useState(null);
    const [currentImageIndex, setCurrentImageIndex] = useState(null);
    const [filters, setFilters] = useState({});
    const [availableFilters, setAvailableFilters] = useState({
        years: [],
        venues: [],
        performers: []
    });
    const [perPage, setPerPage] = useState(12);

    const attributes = JSON.parse(rootElement.dataset.attributes);
    const ajaxUrl = rootElement.dataset.ajaxUrl;
    const nonce = rootElement.dataset.nonce;

    const fetchImages = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                action: 'get_flyer_gallery',
                nonce,
                page: currentPage,
                per_page: perPage,
                ...filters
            });

            const response = await fetch(`${ajaxUrl}?${params}`);
            const data = await response.json();

            if (data.success) {
                setImages(data.data.images);
                setTotalPages(data.data.pages);

                // Update available filters if they exist in the response
                if (data.data.available_filters) {
                    setAvailableFilters(data.data.available_filters);
                }
            }
        } catch (error) {
            console.error('Error fetching images:', error);
        }
        setLoading(false);
    };

    useEffect(() => {
        fetchImages();
    }, [currentPage, filters, perPage]);

    return (
        <div className="flyer-gallery-container">
            <FlyerGalleryFilters
                filters={filters}
                onChange={setFilters}
                availableFilters={availableFilters}
            />

            <FlyerGalleryPagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
                perPage={perPage}
                onPerPageChange={setPerPage}
            />

            <div className="flyer-gallery-grid">
                {loading ? (
                    <div className="flyer-gallery-loading">
                        <div className="flyer-gallery-spinner"></div>
                    </div>
                ) : (
                    images.map((image, index) => (
                        <div
                            key={image.id}
                            className="flyer-gallery-item"
                            onClick={() => {
                                setSelectedImage(image);
                                setCurrentImageIndex(index);
                            }}
                        >
                            <img src={image.thumbnail} alt="{image.alt}" />
                        </div>
                    ))
                )}
            </div>

            <FlyerGalleryPagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
                perPage={perPage}
                onPerPageChange={setPerPage}
            />

            <Lightbox
                image={selectedImage}
                onClose={() => setSelectedImage(null)}
                onPrev={() => {
                    if (currentImageIndex > 0) {
                        setSelectedImage(images[currentImageIndex - 1]);
                        setCurrentImageIndex(currentImageIndex - 1);
                    }
                }}
                onNext={() => {
                    if (currentImageIndex < images.length - 1) {
                        setSelectedImage(images[currentImageIndex + 1]);
                        setCurrentImageIndex(currentImageIndex + 1);
                    }
                }}
                hasPrev={currentImageIndex > 0}
                hasNext={currentImageIndex < images.length - 1}
            />
        </div>
    );
};