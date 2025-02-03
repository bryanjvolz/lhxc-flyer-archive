import { createRoot } from 'react-dom/client';
import React from 'react';
import './styles/frontend.scss';
import { FlyerGallery } from './components/FlyerGallery';

// Initialize galleries
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.flyer-gallery-root').forEach(element => {
        const root = createRoot(element);
        root.render(React.createElement(FlyerGallery, { rootElement: element }));
    });
});

export default FlyerGallery;