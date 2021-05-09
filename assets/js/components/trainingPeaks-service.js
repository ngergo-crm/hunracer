/**
 * @returns {Promise}
 */
export function fetchHomepageData() {
    return new Promise((resolve, reject) => {
        resolve({
            data: {
                tokenAvailable: window.tokenAvailable,
                user: window.user,
                autoRefresh: window.autoRefresh,
                hasWorkouts: window.hasWorkouts,
                athletes: window.athletes,
            },
        });
    });
}
