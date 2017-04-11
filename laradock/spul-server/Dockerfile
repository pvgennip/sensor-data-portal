FROM node:5.11
MAINTAINER Kukua Team <dev@kukua.cc>

WORKDIR /data
COPY ./ /data/
RUN npm install
RUN npm run compile
RUN npm prune --production
RUN touch /tmp/output.log

COPY .env.example .env
RUN chmod 600 .env

EXPOSE 3333
EXPOSE 5555

CMD npm start
