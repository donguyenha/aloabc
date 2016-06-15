#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb

def InsertLink(cursor, connect, value, id):
    sql = "insert into categories(name) values(%s)" % (value)
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
        # should be check from categories table -> return id of categories table
        sql_film = "update films set category_id=%s where id=%d" % (value, id)

    except:
        pass

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select description, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()
for desc in results:
    for i in desc[0].split('\n'):
        if 'Thể loại' in i:
            print i.split(':')[-1].strip()
