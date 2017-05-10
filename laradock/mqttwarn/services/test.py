#!/usr/bin/env python
# -*- coding: utf-8 -*-

import time

def split_ssu_wap_for_influx(tpc, pl):
    out = pl.split(",")
    tpc = "topic=" + tpc.replace('/', '_')
    tst = int(time.time())
    pyl = "%s,sensor_id=%s,type=ssu_wap temp_ssu=%s,temp_wap=%s,pressure_ssu=%s,pressure_wap=%s,bat_v=%s %s" % (tpc, out[0], float(out[1])/10, float(out[4])/10, int(out[2]), int(out[3]), float(out[5])/1000, tst)
    return pyl


def split_hap_sum_for_influx(tpc, pl):
    sensor_id = 1
    out = pl.split(",")
    tpc = "topic=" + tpc.replace('/', '_')
    tst = int(out[0]) if int(out[0]) > 0 else int(time.time())
    pyl = "%s,sensor_id=%s,type=hap_sum co=%s,co2=%s,p1=%s,p2=%s,bat_v=%s %s" % (tpc, sensor_id, float(out[1]), float(out[2]), float(out[3]), float(out[4]), float(out[5]), tst)
    return pyl

    
tp  = "ssu_wap"
pl  = "6d70a5d273205cae,203,1024,1017,199,3677"
tag = ""

# tp  = "hap_sum"
# pl  = "1494335973, 98, 0, 65140, 52924, 168"
# tag = ""

if tp == "ssu_wap":
    tag = ""
    payload = split_ssu_wap_for_influx(tp, pl)
elif tp == "hap_sum":
    tag = ""
    payload = split_hap_sum_for_influx(tp, pl)
else:
    payload = " value="+item.payload


print payload

# SSU Message:    6d70a5d273205cae,203,1024,1017,199,3677
# SSU Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).
# <measurement>,<tag_key>=<tag_value>,<tag_key>=<tag_value> <field_key>=<field_value>,<field_key>=<field_value> <timestamp>
