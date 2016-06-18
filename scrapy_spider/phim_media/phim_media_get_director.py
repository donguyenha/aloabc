#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb

def InsertLink(name, connect, cursor):
    sql = "insert into directors(name) values('%s')" % name
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
        return True
    except MySQLdb.IntegrityError:
        # error with duplicate record, stop crawl next page
        return False

# define connection for DB
connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = 'select description, id from films where status=1'
cursor.execute(sql)
results = cursor.fetchall()

for i in results:
    for j in i[0].split('\n'):
        if u'Đạo diễn' in j:
            #print j
            for director in j.split(':')[-1].strip().split(','):
                # director.title() is camel case text
                InsertLink(director.title(), connect, cursor)
