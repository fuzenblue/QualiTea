const WebSocket = require('ws');
const http = require('http');

// HTTP Server for receiving push notifications from PHP
const server = http.createServer((req, res) => {
    if (req.method === 'POST' && req.url === '/broadcast') {
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });
        req.on('end', () => {
            console.log('Received broadcast:', body);
            // Broadcast to all connected WebSocket clients
            wss.clients.forEach(client => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(body);
                }
            });
            res.writeHead(200, { 'Content-Type': 'text/plain' });
            res.end('Broadcast sent');
        });
    } else {
        res.writeHead(404);
        res.end();
    }
});

// WebSocket Server
const wss = new WebSocket.Server({ server });

wss.on('connection', (ws) => {
    console.log('Client connected');
    ws.on('message', (message) => {
        console.log('Received:', message);
    });
});

server.listen(3000, () => {
    console.log('WebSocket server started on port 3000');
});
