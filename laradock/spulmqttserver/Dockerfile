FROM node:5.11

RUN git clone https://github.com/pvgennip/spul-mqtt-server.git data
RUN curl https://raw.githubusercontent.com/pvgennip/spul-mqtt-server/master/lib/connector.js > /data/lib/connector.js

RUN touch /tmp/output.log

ADD docker-entrypoint.sh /usr/bin/
RUN chmod +x /usr/bin/docker-entrypoint.sh

WORKDIR /data

RUN npm install --production

ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 9007
EXPOSE 9008

CMD ["node", "lib/connector.js"]
