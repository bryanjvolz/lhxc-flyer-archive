import React from "react";
import Select from "react-select";

const PerformerSelect = ({ value, onChange, performers }) => {
  const createOptions = (performersList) => {
    if (!performersList) return [];
    const allPerformers = performersList.reduce((acc, performers) => {
      if (!performers) return acc;
      const individualPerformers = performers.split(",").map((p) => p.trim());
      return [...acc, ...individualPerformers];
    }, []);

    // Remove duplicates and sort
    const uniquePerformers = [...new Set(allPerformers)].sort();
    return uniquePerformers.map((performer) => ({
      value: performer,
      label: performer,
    }));
  };

  return (
    <label
      htmlFor="performers-filter"
      className="filter-select"
    >
      <span class="sr-only">Bands/Performers</span>
      <Select
        id="performers-filter"
        value={value ? { value, label: value } : null}
        onChange={(option) => onChange(option ? option.value : "")}
        options={createOptions(performers)}
        isClearable
        placeholder="Filter Bands/Performers"
      />
    </label>
  );
};

export default PerformerSelect;
