import React from "react";
import ShareEmail from "./ShareEmail";
import ReportFlyer from "./ReportFlyer";

// Change from named export to default export
export default function Lightbox({
  image,
  onClose,
  onPrev,
  onNext,
  hasPrev,
  hasNext,
}) {
  if (!image) return null;

  const renderPerformers = (performers) => {
    if (!performers) return null;
    const performerList = performers.split(",");
    return (
      <div className="performers-list">
        {performerList.map((performer, index) => (
          <span key={index} className="performer-tag">
            {performer.trim()}
            {/* {index !== performerList.length - 1 && ", "} */}
          </span>
        ))}
      </div>
    );
  };

  const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  };

  return (
    <div id="flyer-lightbox" className="flyer-gallery-lightbox" onClick={onClose}>
      <div className="lightbox-content" onClick={(e) => e.stopPropagation()}>
        <div className="image-header">
          <ShareEmail image={image} />
          <ReportFlyer image={image} />
          <button className="close-button" onClick={onClose}>
            &times;
          </button>
        </div>

        <div className="image-container">
          {hasPrev && (
            <button className="nav-button prev" onClick={onPrev}>
              &larr;
            </button>
          )}
          <img src={image.full} alt={image.title} />
          {hasNext && (
            <button className="nav-button next" onClick={onNext}>
              &rarr;
            </button>
          )}
        </div>

        <div className="image-info">
          {image.title && <h3>{image.title}</h3>}
          {image.event_date && (
            <p>
              <strong>Date:</strong> {formatDate(image.event_date)}
            </p>
          )}
          {image.venue && <p><strong>Venue:</strong> {image.venue}</p>}
          {image.artists && <p><strong>Artist(s):</strong> {image.artists}</p>}
          {image.performers && (
            <div className="performers-section">
              <p>
                <strong>Bands/Performers:</strong>
                {renderPerformers(image.performers)}
              </p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
