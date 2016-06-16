#!/usr/bin/env python
# -*- coding: utf-8 -*-
import MySQLdb
import facebook
import sys

fb_token_key = 'EAAYWrk8vdiEBAAa2mohGS1lkaucidjQpyVc1ML54diwg26dT7bRRLNV1FXl5absSo0xKdZA7AjYUi1uE8yRSDdXKJHiSpOZCfvsTabT7EZA9mpTp4csiMtyycCKQM7RRmabIDpbI5l5oYDEOS8RnzSz4IgvkXMZD'
graph = facebook.GraphAPI(access_token=fb_token_key)

#file img upload
filenname = sys.argv[-1].strip()
title = filenname.split('/')[-1]
# remove .jpg from title
title = title.split('.')[0]

# upload img to facebook
# album_path=238338113211606/photos -> ID of Album
# return photo instance
photo = graph.put_photo(image=open(filenname, 'rb'),  message=title, album_path='238338113211606/photos')

# get ID of photo
photoid = '%s?fields=images' % photo['id']
photo_sql = 'update films set fb_id="%s" where id=%s)' % (photo['id'], sys.argv[-2])
cursor.execute(photo_sql)
db.commit()

img_temp = graph.get_object(id=photoid)
count = 1
for i in img_temp['images']:
    source, width, height = i.items()
    photo_sql = 'update films set thumbnail_id%d="%s" where id=%s)' % (count, source[-1], sys.argv[-2])
    count += 1
    cursor.execute(photo_sql)
    db.commit()
