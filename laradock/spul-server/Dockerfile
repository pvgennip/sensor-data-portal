FROM node:5.11
MAINTAINER Kukua Team <dev@kukua.cc>

WORKDIR /data
#COPY ./ /data/

COPY package.json /data/

RUN npm install --production
RUN npm prune --production
RUN touch /tmp/output.log

COPY lib /data/lib/
COPY tools /data/tools/

EXPOSE 3333
EXPOSE 5555

CMD npm start
