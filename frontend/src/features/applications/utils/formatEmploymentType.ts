
export const formatEmploymentType = (value: string): string => {
    const labels: Record<string, string> = {
        full_time: "Full Time",
        part_time: "Part Time",
        contract: "Contract",
        internship: "Internship",
        freelance: "Freelance",
        remote: "Remote",
    };

    return labels[value] ?? value.replace(/_/g, " ");
};
