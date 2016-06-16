#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb
import urllib

def InsertCategory(cursor, connect, value, id):
    for i in value.split(','):
        sql = "insert into categories(name) values(%s)" % (i)
        print sql
        try:
            cursor.execute(sql)
            connect.commit()
            # should be check from categories table -> return id of categories table
            sql_film = "update films set category_id=%s where id=%d" % (value, id)
            print sql_film
        except Exception as e:
            print str(e)
            pass

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select thumbnail_id, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()
folder_path = '/home/hadn'
for desc in results:
    try:
        filename = '%s/%s' % (folder_path, desc[0].split('/')[-1])
        urllib.urlretrieve(desc[0], filename)
        # upload img to facebook
        # code
        # update img of films table
    except:
        print desc[1]
        pass
