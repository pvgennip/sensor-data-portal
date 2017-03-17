FROM ubuntu:14.04
MAINTAINER Fabian Stäber, fabian@fstab.de

RUN apt-get update
RUN apt-get upgrade -y

RUN apt-get -y install nodejs vim git
RUN ln -s /usr/bin/nodejs /usr/bin/node
RUN apt-get -y install npm
RUN npm -y install bower -g

EXPOSE 8000

COPY entrypoint.sh /home/bower/entrypoint.sh
RUN chmod +x /home/bower/entrypoint.sh

#RUN adduser --disabled-login --gecos '' bower
#USER bower
WORKDIR /home/bower
ENTRYPOINT ["/home/bower/entrypoint.sh"]