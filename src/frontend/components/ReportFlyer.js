import React from "react";

const ReportFlyer = ({ image }) => {
  const createReportLink = () => {
    const subject = encodeURIComponent(`LHxC Flyer Gallery - ${image.title}`);
    const adminEmail = encodeURIComponent("admin@louisvillehardcore.com");
    return `mailto:?subject=${subject}&to=${adminEmail}&body=Please describe any issues with the flyer here:`;
  };

  return (
    <a
      href={createReportLink()}
      className="report-flyer-link"
      title="Report issue with this flyer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
      >
        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
        <line x1="4" y1="22" x2="4" y2="15" />
      </svg>
    </a>
  );
};

export default ReportFlyer;
