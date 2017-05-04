FROM node:5.11

RUN git clone https://github.com/pvgennip/spul-mqtt-server.git data
RUN curl https://raw.githubusercontent.com/pvgennip/spul-mqtt-server/master/lib/connector.js > /data/lib/connector.js

WORKDIR /data

RUN npm install --production
RUN touch /tmp/output.log

# Find the .env file for settings
ENV PATH "$PATH:/data"

EXPOSE 9007
EXPOSE 9008

CMD ["node", "lib/connector.js"]
