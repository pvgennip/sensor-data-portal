FROM node:5.11

WORKDIR /data

RUN git clone https://github.com/pvgennip/concava-connector-spul.git
RUN npm install --production
RUN touch /tmp/output.log

EXPOSE 3333
EXPOSE 5555

CMD npm start
