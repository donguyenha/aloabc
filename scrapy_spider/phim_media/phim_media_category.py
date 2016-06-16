#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb
import re

def InsertCategory(cursor, connect, value):
    for i in value.split(','):
        sql = "insert into categories(name) values('%s')" % (i.strip())
        try:
            cursor.execute(sql)
            connect.commit()
        except Exception as e:
            print str(e)
            pass

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select description, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()
for desc in results:
    try:
        for i in desc[0].split('\n'):
            if len(i) < 30:
                if u'Thể lo' in i:
                    value = i[-1].strip()
                    InsertCategory(cursor, connect, value)
                    break
    except Exception as e:
        print str(e)
        pass
