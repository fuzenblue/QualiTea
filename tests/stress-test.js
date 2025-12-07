import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    scenarios: {
        browse_menu: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10s', target: 50 },
                { duration: '20s', target: 50 },
                { duration: '10s', target: 0 },
            ],
            gracefulRampDown: '0s',
            exec: 'browseMenu',
        },
        book_queue: {
            executor: 'constant-arrival-rate',
            rate: 10, // 10 bookings per second
            timeUnit: '1s',
            duration: '30s',
            preAllocatedVUs: 10,
            maxVUs: 50,
            exec: 'bookQueue',
        },
    },
    thresholds: {
        http_req_duration: ['p(95)<500'], // 95% of requests should be below 500ms
        http_req_failed: ['rate<0.01'],   // Less than 1% errors
    },
};

const BASE_URL = 'http://qualitea_nginx';

export function browseMenu() {
    const res = http.get(`${BASE_URL}/api/teas`);
    check(res, {
        'menu status is 200': (r) => r.status === 200,
        'menu has content': (r) => r.json().length > 0,
    });
    sleep(1);
}

export function bookQueue() {
    const payload = JSON.stringify({
        customer_name: `User-${__VU}-${__ITER}`,
        phone: `08${Math.floor(Math.random() * 100000000)}`,
        note: 'Load Test'
    });

    const params = {
        headers: { 'Content-Type': 'application/json' },
    };

    const res = http.post(`${BASE_URL}/api/bookings`, payload, params);

    check(res, {
        'booking status is 200': (r) => r.status === 200,
        'got queue number': (r) => r.json('queue_number') !== undefined,
    });
}
