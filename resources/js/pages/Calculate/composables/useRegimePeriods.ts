const days360 = (startDate: string, endDate: string) => {
    const start = new Date(`${startDate}T00:00:00`);
    const end = new Date(`${endDate}T00:00:00`);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return 0;
    }

    const startDay = Math.min(start.getDate(), 30);
    const endDay = Math.min(end.getDate(), 30);

    return (
        (end.getFullYear() - start.getFullYear()) * 360 +
        (end.getMonth() - start.getMonth()) * 30 +
        (endDay - startDay)
    );
};

export const useRegimePeriods = () => {
    const calculateYearsBetweenDates = (startDate: string, endDate: string) =>
        days360(startDate, endDate) / 360;

    return {
        calculateYearsBetweenDates,
    };
};
