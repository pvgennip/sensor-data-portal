# SSU Message:    6d70a5d273205cae,203,1024,1017,199,3677
# SSU Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).
# <measurement>,<tag_key>=<tag_value>,<tag_key>=<tag_value> <field_key>=<field_value>,<field_key>=<field_value> <timestamp>
tp  = "ssu"
pl  = "6d70a5d273205cae,203,1024,1017,199,3677"
tag = ""
out = pl.split(",")
tpc = "topic=" + tp.replace('/', '_')
payload = "%s,sensor_id=%s,type=ssu_wap temp_ssu=%s,temp_wap=%s,pressure_ssu=%s,pressure_wap=%s,bat_v=%s" % (tpc, out[0], float(out[1])/10, float(out[4])/10, out[2], out[3], float(out[5])/1000)
print payload