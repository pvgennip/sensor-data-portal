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

    srv.logging.debug("Measurement: %s, Payload: %s", measurement, payload)

    if item.target == "ssu":
        # SSU Message:    6d70a5d273205cae,203,1024,1017,199,3677
        # SSU Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).
        # <measurement>,<tag_key>=<tag_value>,<tag_key>=<tag_value> <field_key>=<field_value>,<field_key>=<field_value> <timestamp>
        tag = ""
        out = item.payload.split(",")
        tpc = "topic=" + item.topic.replace('/', '_')
        payload = tpc+",sensor_id="+out[0]+",type=ssu_wap temp_ssu="+out[1]/10+",temp_wap="+out[4]/10+",pressure_ssu="+out[2]+",pressure_wap="+out[3]+",bat_v="+out[5]/1000
    elif item.target == "hap":
        tag = ""
        payload = item.payload
    else:
        payload = item.payload

    srv.logging.debug("Measurement: %s, Payload after parsing: %s", measurement, payload)
    
    try:
        url = "http://%s:%d/write?db=%s" % (host, port, database)
        data = measurement + ',' + tag + payload

        srv.logging.debug("Data to be send to Influx: %s" % (data))
        
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

