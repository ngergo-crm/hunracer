import moment from 'moment';

/**
 * @param {date|moment} start The start date
 * @param {date|moment} end The end date
 * @param {string} type The range type. eg: 'days', 'hours' etc
 * @param {string} format The format type. eg: 'Y-MM-DD' etc
 */
export function getRange(startDate, endDate, type, format) {
    const fromDate = moment(startDate);
    const toDate = moment(endDate);
    const diff = toDate.diff(fromDate, type);
    const weeks = [];
    let weekPointer = -1;
    for (let i = 0; i <= diff; i++) {
        const newDate = moment(startDate).lang('hu').add(i, type); //.format('Y. MMMM D.')
        if (newDate.format('dddd') === 'hétfő') {
            weekPointer += 1;
            weeks.push({ days: [], summary: {} });
        }
        weeks[weekPointer].days.push({ date: newDate, workouts: [] });
    }
    return weeks;
}

/**
 *
 * @param unit
 * @return {string}
 */
export function getUnit(unit, axis = false) {
    let measurement = '';
    if (unit === 'distance') {
        measurement = 'km';
        if (axis) {
            measurement = 'Távolság (km)';
        }
    } else if (unit === 'elevation') {
        measurement = 'm';
        if (axis) {
            measurement = 'Szintemelkedés (m)';
        }
    } else if (unit === 'totalTime') {
        measurement = 'h';
        if (axis) {
            measurement = 'Idő (h)';
        }
    } else if (unit === 'energy') {
        measurement = 'kJ';
        if (axis) {
            measurement = 'Energia (kJ)';
        }
    } else if (unit === 'tss') {
        measurement = 'tss';
    }
    return measurement;
}

export function calendarMaxDate() {
    return moment().endOf('isoWeek').format('YYYY-MM-DD');
}

export function initialDate() {
    return moment().format('YYYY-MM-DD');
}
