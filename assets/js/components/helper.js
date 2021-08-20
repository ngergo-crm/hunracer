import moment from 'moment';
import { fetchSettings } from '@/components/settings';

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

export function containsObject(array, field, value) {
    return array.some((o) => o[field] === value);
}

export function getSetting(source, key) {
    return source.find((config) => config.settingKey === key);
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

export function findIndex(array, object, arrayoffset = 'hydra:member', offsetname = '@id') {
    return array.findIndex((element) => {
        if (element[offsetname] === object[offsetname]) {
            return true;
        }
    });
}

export function calculateURating(birthdate, noURation = 'nincs') {
    //workoutYearStart should be sent by controller
    let workoutYearBegins = window.workoutYearStart ? window.workoutYearStart : '2020-11-01';
    workoutYearBegins = `YYYY-${moment(workoutYearBegins).format('MM-DD').toString()}`;
    console.log(window.workoutYearStart);
    console.log(workoutYearBegins);
    let URating;
    if (moment(birthdate).format('YYYY-MM-DD') === 'Invalid date') {
        URating = noURation;
    } else {
        const change = moment().format(workoutYearBegins);
        const age = moment(change).diff(birthdate, 'years');
        if (age === 10 || age === 11) {
            URating = 'U-11';
        } else if (age === 12 || age === 13) {
            URating = 'U-13';
        } else if (age === 14 || age === 15) {
            URating = 'U-15';
        } else if (age === 16 || age === 17) {
            URating = 'U-17';
        } else if (age === 18 || age === 19) {
            URating = 'U-19';
        } else if (age > 19) {
            URating = 'U-besorolás alatt';
        } else if (age < 11) {
            URating = 'U-besorolás fölött';
        }
    }
    return URating;
}

export function formatRole(roleDescription) {
    let formattedRole = '';
    if (roleDescription === 'szuperAdmin') {
        formattedRole = 'Szuperadmin';
    } else if (roleDescription === 'admin') {
        formattedRole = 'Admin';
    } else if (roleDescription === 'edző') {
        formattedRole = 'Edző';
    } else if (roleDescription === 'sportoló') {
        formattedRole = 'Sportoló';
    }
    return formattedRole;
}

export function getRoleByRoleDescription(roleDescription) {
    let formattedRole = '';
    if (roleDescription === 'admin') {
        formattedRole = 'ROLE_ADMIN';
    } else if (roleDescription === 'edző') {
        formattedRole = 'ROLE_TRAINER';
    } else if (roleDescription === 'sportoló') {
        formattedRole = 'ROLE_USER';
    }
    return formattedRole;
}
