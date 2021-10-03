/**
 * @returns {Promise}
 */
export function fetchDataSheetData() {
    return new Promise((resolve, reject) => {
        resolve({
            data: {
                athlete: window.athlete,
            },
        });
    });
}
