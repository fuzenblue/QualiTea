const express = require('express');
const axios = require('axios');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());

const LINE_NOTIFY_TOKEN = process.env.LINE_NOTIFY_TOKEN;

if (!LINE_NOTIFY_TOKEN) {
    console.error('LINE_NOTIFY_TOKEN is missing');
    process.exit(1);
}

app.post('/alert', async (req, res) => {
    const alerts = req.body.alerts || [];

    for (const alert of alerts) {
        const message = `
🚨 **${alert.status.toUpperCase()}**: ${alert.labels.alertname}
Severity: ${alert.labels.severity}
Description: ${alert.annotations.description}
        `.trim();

        try {
            await axios.post('https://notify-api.line.me/api/notify',
                `message=${encodeURIComponent(message)}`,
                {
                    headers: {
                        'Authorization': `Bearer ${LINE_NOTIFY_TOKEN}`,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }
            );
            console.log('Sent notification to Line');
        } catch (error) {
            console.error('Error sending to Line:', error.message);
        }
    }

    res.status(200).send('OK');
});

app.listen(8080, () => {
    console.log('Line Bridge running on 8080');
});
