FROM node:5.11

WORKDIR /data

RUN npm install https://github.com/pvgennip/concava-connector-spul.git --production
RUN touch /tmp/output.log

EXPOSE 3333
EXPOSE 5555

CMD npm start
