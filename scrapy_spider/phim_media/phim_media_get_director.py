#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb

def InsertLink(name, cursor, connect):
    sql = "insert into directors values('%s')" % name
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
        return True
    except MySQLdb.IntegrityError:
        # error with duplicate record, stop crawl next page
        return False

# define connection for DB
connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc');
cursor = connect.cursor()

sql = 'select description from films where status=1'
cursor.execute(sql)
results = cursor.fetchall()

for i in results:
    for j in i[0].split('\n'):
        if u'Đạo diễn' in j:
            for x in j.split(','):
                InsertLink(x.title(), connect, cursor)
                #print j.title()
                #print j.lower()
