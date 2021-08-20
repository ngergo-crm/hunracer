/**
 * @returns {Promise}
 */
export function fetchSettings() {
    return new Promise((resolve, reject) => {
        resolve({
            data: {
                workoutYearStart: window.workoutYearStart ? window.workoutYearStart : '2020-11-01',
            },
        });
    });
}
