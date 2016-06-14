# -*- coding: utf-8 -*-
import scrapy
from tutor_scrapy.items import TutorScrapyItem


class BasicSpider(scrapy.Spider):
    name = "basic"
    allowed_domains = ["phim.media"]
    start_urls = (
        'http://www.phim.media',
    )

    def parse(self, response):
        #self.log("title: %s" % response.xpath('/html/head/title').extract())
        item = TutorScrapyItem()
        item['title'] = response.xpath('/html/head/title').extract()[0]
        print response.xpath('/html/head/title').extract()[0]
        return item

        # use items loader
        itemloader = ItemLoader(item=TutorScrapyItem(), response=response)
        itemloader.add_xpath('title', '/html/head/title')
        return itemloader.load_item()

        #pass
