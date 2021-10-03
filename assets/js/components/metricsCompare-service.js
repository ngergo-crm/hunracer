/**
 * @returns {Promise}
 */
export function fetchMetricsCompare() {
    return new Promise((resolve, reject) => {
        resolve({
            data: {
                user: window.user,
            },
        });
    });
}
