import React from 'react';
import Select from 'react-select';

const PerformerSelect = ({ value, onChange, performers }) => {
    const createOptions = (performersList) => {
        if (!performersList) return [];
        const allPerformers = performersList.reduce((acc, performers) => {
            if (!performers) return acc;
            const individualPerformers = performers.split(',').map(p => p.trim());
            return [...acc, ...individualPerformers];
        }, []);

        // Remove duplicates and sort
        const uniquePerformers = [...new Set(allPerformers)].sort();
        return uniquePerformers.map(performer => ({ value: performer, label: performer }));
    };

    return (
        <>
            <label class="sr-only" htmlFor="performers-filter">Bands/Performers</label>
            <Select
                id="performers-filter"
                className="filter-select"
                value={value ? { value, label: value } : null}
                onChange={(option) => onChange(option ? option.value : '')}
                options={createOptions(performers)}
                isClearable
                placeholder="Select Band"
            />
        </>
    );
};

export default PerformerSelect;