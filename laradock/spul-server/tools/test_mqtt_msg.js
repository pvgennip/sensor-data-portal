const mqtt = require('mqtt');
const mqttHost = (process.env['MQTT_HOST'] || 'localhost')
var client = mqtt.connect('mqtt://' + mqttHost, {clientId: "0000000000000998", password: "", connectTimeout: 3000, })
client.on('connect', () => { client.publish('data', 'Final is een test') })
client.end()