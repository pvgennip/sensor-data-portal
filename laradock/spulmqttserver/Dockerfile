FROM node:5.11

RUN git clone https://github.com/pvgennip/spul-mqtt-server.git data

WORKDIR /data

RUN npm install --production
RUN touch /tmp/output.log

EXPOSE 3333
EXPOSE 5555

CMD ["node", "lib/connector.js"]
