const mqtt = require('mqtt');
const mqttHost = (process.env['MQTT_HOST'] || '37.48.120.157')
var client = mqtt.connect('mqtt://' + mqttHost, {clientId: "0000000000000998", password: "", connectTimeout: 3000, })
client.on('connect', () => { client.publish('data', 'Hey, weer is een test') })
client.end()