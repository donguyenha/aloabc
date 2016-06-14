# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy
from scrapy.item import Item, Field


#from scrapy.item import Item, Field
#class PropertiesItem(Item):
# # Primary fields
# title = Field()
# price = Field()
# description = Field()
# address = Field()
# image_urls = Field()
 # Calculated fields
# images = Field()
# location = Field()
 # Housekeeping fields
# url = Field()
# project = Field()
# spider = Field()
# server = Field()
# date = Field()

class TutorItem(scrapy.Item):
    # define the fields for your item here like:
    # name = scrapy.Field()
    # Primary fields
    title = Field()

    pass
