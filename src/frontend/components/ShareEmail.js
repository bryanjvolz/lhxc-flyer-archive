import React from 'react';

const ShareEmail = ({ image }) => {
    const createMailtoLink = () => {
        const subject = encodeURIComponent(`Check out this flyer: ${image.title}`);
        const body = encodeURIComponent(
            `Event Details:\n` +
            `${image.title}\n` +
            `Date: ${image.event_date}\n` +
            `Venue: ${image.venue}\n` +
            `Performers: ${image.performers}\n`
        );
        return `mailto:?subject=${subject}&body=${body}`;
    };

    return (
        <a href={createMailtoLink()} className="share-email-link" title="Share via Email">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </a>
    );
};

export default ShareEmail;