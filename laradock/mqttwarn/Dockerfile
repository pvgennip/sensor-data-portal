FROM python:2.7

MAINTAINER Andrés Sánchez García <asg1612@gmail.com>


RUN apt-get update &&\
    apt-get install -y git python-pip &&\
    apt-get clean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* &&\
    pip install paho-mqtt slacker


WORKDIR /opt/
RUN git clone https://github.com/jpmens/mqttwarn.git
ADD services/influxdb_akvo.py /opt/mqttwarn/services/
ADD mqttwarn.ini /opt/mqttwarn/mqttwarn.ini.sample

#ADD docker-entrypoint.sh /opt/mqttwarn/
#RUN chmod +x /opt/mqttwarn/docker-entrypoint.sh

WORKDIR /opt/mqttwarn/
#ENTRYPOINT ["/opt/mqttwarn/docker-entrypoint.sh"]

CMD ["python", "mqttwarn.py"]