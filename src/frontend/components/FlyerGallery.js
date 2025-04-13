import React, { useState, useEffect } from "react";
import Select from "react-select";
import Lightbox from "./Lightbox"; // Remove the curly braces
import PerformerSelect from "./PerformerSelect";

const FlyerGalleryFilters = ({
  filters,
  onChange,
  availableFilters,
  onPageChange,
  setCurrentPage,
}) => {
  const createOptions = (values) => {
    return values.map((value) => ({ value, label: value }));
  };
  const handleFilterChange = (newFilters) => {
    onChange(newFilters);
    // onPageChange(1);
    setCurrentPage(1);
    // setCurrentPage(1); // Reset to first page to prevent it looking like there's no results
  };

  return (
    <div className="flyer-gallery-filters">
      <label
        htmlFor="year-filter"
        className="filter-select"
      >
        <span class="sr-only">Year</span>
        <Select
          id="year-filter"
          value={
            filters.year ? { value: filters.year, label: filters.year } : null
          }
          onChange={(option) =>
            handleFilterChange({ ...filters, year: option ? option.value : "" })
          }
          options={createOptions(availableFilters.years)}
          isClearable
          placeholder="Filter Year"
        />
      </label>

      <label htmlFor="venue-filter" className="filter-select">
        <span class="sr-only">Venues</span>
        <Select
          id="venue-filter"
          value={
            filters.venue
              ? { value: filters.venue, label: filters.venue }
              : null
          }
          onChange={(option) =>
            handleFilterChange({
              ...filters,
              venue: option ? option.value : "",
            })
          }
          options={createOptions(availableFilters.venues)}
          isClearable
          placeholder="Filter Venue"
        />
      </label>

      <PerformerSelect
        value={filters.performer}
        onChange={(value) =>
          handleFilterChange({ ...filters, performer: value })
        }
        performers={availableFilters.performers}
      />
    </div>
  );
};

const FlyerGalleryPagination = ({
  currentPage,
  totalPages,
  onPageChange,
  perPage,
  onPerPageChange,
}) => {
  return (
    <div className="flyer-gallery-pagination">
      <label className="per-page-select">
        <select
          value={perPage}
          onChange={(e) => onPerPageChange(parseInt(e.target.value))}
        >
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="200">200</option>
        </select>
        <span>per page</span>
      </label>

      <div className="pagination-controls">
        {currentPage > 1 && (
          <button onClick={() => onPageChange(currentPage - 1)}>
            Previous
          </button>
        )}
        <span>
          {currentPage} / {totalPages}
        </span>
        {currentPage < totalPages && (
          <button onClick={() => onPageChange(currentPage + 1)}>Next</button>
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
    performers: [],
  });
  const [perPage, setPerPage] = useState(20);
  const ajaxUrl = rootElement.dataset.ajaxUrl;
  const nonce = rootElement.dataset.nonce;

  const fetchImages = async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({
        action: "get_flyer_gallery",
        nonce,
        page: currentPage,
        per_page: perPage,
        ...filters,
      });

      const response = await fetch(`${ajaxUrl}?${params}`);
      const data = await response.json();

      if (data.success) {
        setImages(data.data.images);
        setTotalPages(data.data.pages);

        if (data.data.available_filters) {
          setAvailableFilters(data.data.available_filters);
        }
      }
    } catch (error) {
      console.error("Error fetching images:", error);
    }
    setLoading(false);
  };

  // Initial load effect
  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const flyerId = params.get("flyer");

    if (flyerId) {
      // First fetch the single flyer
      const fetchSingleFlyer = async () => {
        try {
          const params = new URLSearchParams({
            action: "get_flyer",
            nonce,
            id: flyerId,
          });

          const response = await fetch(`${ajaxUrl}?${params}`);
          const data = await response.json();

          if (data.success && data.data.image) {
            setSelectedImage(data.data.image);
            document.body.style.overflowY = "hidden";
            document.body.style.position = "fixed";
          }
        } catch (error) {
          console.error("Error fetching single flyer:", error);
        }
        // Load the gallery after fetching the single flyer
        fetchImages();
      };
      fetchSingleFlyer();
    } else {
      fetchImages();
    }
  }, []);

  // Regular fetch for page/filter changes
  useEffect(() => {
    if (!loading) { // Prevent double fetch during initial load
      fetchImages();
    }
  }, [currentPage, filters, perPage]);

  useEffect(() => {
    // Check for flyer ID in URL on initial load
    const params = new URLSearchParams(window.location.search);
    const flyerId = params.get("flyer");
    if (flyerId) {
      const image = images.find((img) => img.id === parseInt(flyerId));
      if (image) {
        setSelectedImage(image);
        setCurrentImageIndex(images.indexOf(image));
      }
    }
  }, [images]);

  const handleImageSelect = (image, index) => {
    setSelectedImage(image);
    setCurrentImageIndex(index);
    // Update URL with flyer ID and prevent body scroll
    const newUrl = new URL(window.location);
    newUrl.searchParams.set("flyer", image.id);
    window.history.pushState({}, "", newUrl);
    document.body.style.overflowY = "hidden";
    document.body.style.position = "fixed";
  };

  const handleLightboxClose = () => {
    setSelectedImage(null);
    setCurrentImageIndex(null);
    // Remove flyer ID from URL and restore body scroll
    const newUrl = new URL(window.location);
    newUrl.searchParams.delete("flyer");
    window.history.pushState({}, "", newUrl);
    document.body.style.overflowY = "auto";
    document.body.style.position = "";
  };

  // Add keyboard event listener for Esc close
  useEffect(() => {
    const handleEscKey = (event) => {
      if (event.key === "Escape" && selectedImage) {
        handleLightboxClose();
      }
    };

    document.addEventListener("keydown", handleEscKey);
    return () => {
      document.removeEventListener("keydown", handleEscKey);
    };
  }, [selectedImage]);

  // Add cleanup on component unmount
  useEffect(() => {
    return () => {
      document.body.style.overflow = "auto";
    };
  }, []);

  return (
    <div className="flyer-gallery-container">
      <FlyerGalleryFilters
        filters={filters}
        onChange={setFilters}
        availableFilters={availableFilters}
        currentPage={currentPage}
        setCurrentPage={setCurrentPage}
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
        ) : images.length === 0 ? (
          <div className="flyer-gallery-empty">
            <p>No flyers found. Try adjusting your filters.</p>
          </div>
        ) : (
          images.map((image, index) => (
            <button
              key={image.id}
              className="flyer-gallery-item"
              onClick={() => handleImageSelect(image, index)}
              aria-expanded={selectedImage && selectedImage.id === image.id}
              aria-label={`Flyer ${image.id}`}
              tabIndex={selectedImage && selectedImage.id === image.id ? 0 : -1}
            >
              <img
                src={image.thumbnail}
                alt={
                  image.alt
                    ? image.alt
                    : "Punk/Hardcore Flyer from " + image.event_date
                }
              />
            </button>
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
        onClose={handleLightboxClose}
        onPrev={() => {
          if (currentImageIndex > 0) {
            handleImageSelect(
              images[currentImageIndex - 1],
              currentImageIndex - 1
            );
          }
        }}
        onNext={() => {
          if (currentImageIndex < images.length - 1) {
            handleImageSelect(
              images[currentImageIndex + 1],
              currentImageIndex + 1
            );
          }
        }}
        hasPrev={currentImageIndex > 0}
        hasNext={currentImageIndex < images.length - 1}
      />
    </div>
  );
};
