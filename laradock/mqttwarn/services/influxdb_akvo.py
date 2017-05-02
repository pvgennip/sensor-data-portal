#!/usr/bin/env python
# -*- coding: utf-8 -*-

__author__    = 'Pim van Gennip <pim@iconize.nl>'
__copyright__ = 'Copyright 2017 Pim van Gennip'
__license__   = """Eclipse Public License - v 1.0 (http://www.eclipse.org/legal/epl-v10.html)"""

import requests
import logging

# disable info logging in requests module (e.g. connection pool message for every post request)
logging.getLogger("requests").setLevel(logging.WARNING)

# item = {
#    'service'       : 'string',       # name of handling service (`twitter`, `file`, ..)
#    'target'        : 'string',       # name of target (`o1`, `janejol`) in service
#    'addrs'         : <list>,         # list of addresses from SERVICE_targets
#    'config'        : dict,           # None or dict from SERVICE_config {}
#    'topic'         : 'string',       # incoming topic branch name
#    'payload'       : <payload>       # raw message payload
#    'message'       : 'string',       # formatted message (if no format string then = payload)
#    'data'          : (only if JSON payload is detected),           # dict with transformation data
#       {
#         'topic'         : topic name
#         'payload'       : topic payload
#         '_dtepoch'      : epoch time                  # 1392628581
#         '_dtiso'        : ISO date (UTC)              # 2014-02-17T10:38:43.910691Z
#         '_dthhmm'       : timestamp HH:MM (local)     # 10:16
#         '_dthhmmss'     : timestamp HH:MM:SS (local)  # 10:16:21
#       }
#    'title'         : 'mqttwarn',     # possible title from title{}
#    'priority'      : 0,              # possible priority from priority{}
# }

def plugin(srv, item):
    ''' addrs: (measurement) '''

    srv.logging.debug("*** MODULE=%s: service=%s, target=%s", __file__, item.service, item.target)

    host        = item.config['host']
    port        = item.config['port']
    username    = item.config['username']
    password    = item.config['password']
    database    = item.config['database']

    measurement = item.addrs[0]
    tag         = "topic=" + item.topic.replace('/', '_')

    payload = ""
    if (measurement == "ssu")
        payload = split_ssu_wap_for_influx(item.payload)
    else if (measurement == "hap")
        payload = split_hap_sum_for_influx(item.payload)
    
    try:
        url = "http://%s:%d/write?db=%s" % (host, port, database)
        data = measurement + ',' + tag + ' ' + payload
        
        if username is None:
            r = requests.post(url, data=data)
        else:
            r = requests.post(url, data=data, auth=(username, password))
        
        # success
        if r.status_code == 204:
            return True
            
        # request accepted but couldn't be completed (200) or failed (otherwise)
        if r.status_code == 200:
            srv.logging.warn("POST request could not be completed: %s" % (r.text))
        else:
            srv.logging.warn("POST request failed: (%s) %s" % (r.status_code, r.text))
        
    except Exception, e:
        srv.logging.warn("Failed to send POST request to InfluxDB server using %s: %s" % (url, str(e)))

    return False



def split_ssu_wap_for_influx(payload):

    out = payload.split(",")
    return "sensor_id=",out[0],", type=ssu_wap temp_ssu=",out[1]/10," temp_wap=",out[4]/10," pressure_ssu=",out[2]," pressure_wap=",out[3]," bat_v=",out[5]/1000


def split_hap_sum_for_influx(payload):
    return payload



