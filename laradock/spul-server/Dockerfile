FROM node:5.11
MAINTAINER Kukua Team <dev@kukua.cc>

WORKDIR /data
COPY ./ /data/
RUN npm install
RUN npm run compile
RUN npm prune --production
RUN touch /tmp/output.log

ADD .env.example /data/.env
RUN chmod 600 /data/.env

EXPOSE 3333
EXPOSE 5555

CMD npm start
