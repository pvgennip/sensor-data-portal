FROM node:5.11
MAINTAINER Kukua Team <dev@kukua.cc>

WORKDIR /data
COPY ./ /data/

RUN npm install --production
RUN npm prune --production
RUN touch /tmp/output.log

ADD lib /data/lib/
ADD tools /data/tools/

EXPOSE 3333
EXPOSE 5555

CMD npm start
