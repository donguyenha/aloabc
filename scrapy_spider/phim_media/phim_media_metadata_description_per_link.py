#!/usr/bin/env python
# -*- coding: utf-8 -*-
import requests
from bs4 import BeautifulSoup
import MySQLdb
import sys
import re

def GetContent(url):
    data = requests.get(url)
    soup = BeautifulSoup(data.content, "html.parser")
    return soup

def InsertLink(cursor, connect, col, value, id):
    sql = "update films set %s='%s' where id=%d" % (col, re.sub('"', '', value), id)
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
    except Exception as e:
        print str(e)
        pass

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select crawler_at, id from films where crawler_at='%s'" % sys.argv[-1]
cursor.execute(sql)
results = cursor.fetchall()
print results[0]
for url in results:
    soup = GetContent(url[0])
    description = soup.find('div', {'class': 'detail-content-main'}).text.strip()
    InsertLink(cursor, connect, "description", re.sub("'", '', description), int(url[1]))
